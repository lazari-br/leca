<?php

namespace App\Http\Controllers;

use App\Models\ProductVariation;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Installment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesController extends Controller
{
    public function index()
    {
        $sales = Sale::with('items')->orderBy('sale_date', 'desc')->get();
        return view('admin.sales.index', compact('sales'));
    }

    public function create()
    {
        $products = Product::with('variations')->get();
        return view('admin.sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'payment_date' => 'required|date',
            'customer_name' => 'nullable|string|max:255',
            'sale_date' => 'required|date',
            'payment_method' => 'required|string|max:255',
            'installments' => 'nullable|integer|min:1',
            'installment_value' => 'nullable|numeric|min:0',
            'commission_value' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variation_id' => 'required|exists:product_variations,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($data) {
            $total = collect($data['items'])->sum(fn($item) => $item['quantity'] * $item['unit_price']);

            // Calcular comissão se o usuário for vendedor
            $commissionValue = 0;
            $seller_id = null;
            if (auth()->user()->user_type === 'vendedor' && auth()->user()->commission) {
                $commissionValue = $total * (auth()->user()->commission / 100);
                $seller_id = Auth::id();
            }

            $sale = Sale::create([
                'payment_date' => now()->parse($data['payment_date'])->format('Y-m-d'),
                'customer_name' => $data['customer_name'] ?? null,
                'sale_date' => now()->parse($data['sale_date'])->format('Y-m-d'),
                'payment_method' => $data['payment_method'],
                'installments' => $data['installments'] ?? 1,
                'installment_value' => $data['installment_value'] ?? $total,
                'commission_value' => $commissionValue,
                'total' => $total,
                'status' => '-',
                'seller_id' => $seller_id
            ]);

            foreach ($data['items'] as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'product_variation_id' => $item['product_variation_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            // Criar parcelas
            $this->createInstallments($sale, $total, $data, $commissionValue);
        });

        return redirect()->route('admin.sales.index')->with('success', 'Venda registrada com sucesso.');
    }

    public function edit($id)
    {
        $sale = Sale::with('items')->findOrFail($id);
        $products = Product::with('variations')->get();
        return view('admin.sales.edit', compact('sale', 'products'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
            'sale_date' => 'required|date',
            'payment_method' => 'required|string|max:255',
            'installments' => 'nullable|integer|min:1',
            'installment_value' => 'nullable|numeric|min:0',
            'commission_value' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variation_id' => 'required|exists:product_variations,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($data, $id) {
            $sale = Sale::findOrFail($id);
            $total = collect($data['items'])->sum(fn($item) => $item['quantity'] * $item['unit_price']);

            // Calcular comissão se o usuário for vendedor
            $commissionValue = 0;
            if (auth()->user()->user_type === 'vendedor' && auth()->user()->commission) {
                $commissionValue = $total * (auth()->user()->commission / 100);
            }

            $sale->update([
                'customer_name' => $data['customer_name'] ?? null,
                'payment_date' => $data['payment_date'],
                'sale_date' => $data['sale_date'],
                'payment_method' => $data['payment_method'],
                'installments' => $data['installments'] ?? 1,
                'installment_value' => $data['installment_value'] ?? $total,
                'commission_value' => $commissionValue,
                'total' => $total,
            ]);

            // Remover itens e parcelas antigas
            $sale->items()->delete();
            Installment::where('sale_id', $sale->id)->delete();

            foreach ($data['items'] as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'product_variation_id' => $item['product_variation_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            // Recriar parcelas
            $this->createInstallments($sale, $total, $data, $commissionValue);
        });

        return redirect()->route('admin.sales.index')->with('success', 'Venda atualizada com sucesso.');
    }

    public function destroy($id)
    {
        $sale = Sale::findOrFail($id);

        DB::transaction(function () use ($sale) {
            $sale->items()->delete();
            Installment::where('sale_id', $sale->id)->delete();
            $sale->delete();
        });

        return redirect()->route('admin.sales.index')->with('success', 'Venda removida com sucesso.');
    }

    /**
     * Criar parcelas para a venda
     */
    private function createInstallments($sale, $total, $data, $commissionValue = 0)
    {
        $installments = $data['installments'] ?? 1;
        $paymentDate = Carbon::parse($data['payment_date']);

        // Calcular valor de cada parcela
        $installmentAmount = $total / $installments;

        // Calcular comissão por parcela
        $commissionPerInstallment = $commissionValue / $installments;

        // Ajustar última parcela para compensar arredondamentos
        $totalInstallments = ($installments - 1) * $installmentAmount;
        $lastInstallmentAmount = $total - $totalInstallments;

        $totalCommissionInstallments = ($installments - 1) * $commissionPerInstallment;
        $lastCommissionAmount = $commissionValue - $totalCommissionInstallments;

        for ($i = 1; $i <= $installments; $i++) {
            // Calcular data de vencimento (primeira parcela na data de pagamento, demais mensalmente)
            $dueDate = $paymentDate->copy()->addMonths($i - 1);

            // Valor da parcela (última parcela pode ter valor diferente por causa do arredondamento)
            $amount = ($i === $installments) ? $lastInstallmentAmount : $installmentAmount;
            $commissionAmount = ($i === $installments) ? $lastCommissionAmount : $commissionPerInstallment;

            Installment::create([
                'sale_id' => $sale->id,
                'purchase_id' => null,
                'due_date' => $dueDate->format('Y-m-d'),
                'amount' => round($amount, 2),
                'commission_value' => round($commissionAmount, 2),
                'status' => 'pending', // pending, paid, overdue
            ]);
        }
    }
}
