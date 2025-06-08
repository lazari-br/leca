<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Installment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('items')->orderBy('purchase_date', 'desc')->get();
        return view('admin.purchases.index', compact('purchases'));
    }

    public function create()
    {
        $products = Product::with('variations')->get();
        return view('admin.purchases.create', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_name' => 'nullable|string|max:255',
            'purchase_date' => 'required|date',
            'payment_method' => 'required|string|max:255',
            'installments' => 'nullable|integer|min:1',
            'installment_value' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variation_id' => 'required|exists:product_variations,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($data) {
            $total = collect($data['items'])->sum(fn($item) => $item['quantity'] * $item['unit_price']);

            $purchase = Purchase::create([
                'supplier_name' => $data['supplier_name'] ?? null,
                'purchase_date' => now()->parse($data['purchase_date'])->format('Y-m-d'),
                'payment_method' => $data['payment_method'],
                'installments' => $data['installments'] ?? 1,
                'installment_value' => $data['installment_value'] ?? $total,
                'total' => $total,
                'status' => '-',
            ]);

            foreach ($data['items'] as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'product_variation_id' => $item['product_variation_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            // Criar parcelas
            $this->createInstallments($purchase, $total, $data);
        });

        return redirect()->route('admin.purchases.index')->with('success', 'Compra registrada com sucesso.');
    }

    public function edit($id)
    {
        $purchase = Purchase::with('items')->findOrFail($id);
        $products = Product::with('variations')->get();
        return view('admin.purchases.edit', compact('purchase', 'products'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'supplier_name' => 'nullable|string|max:255',
            'purchase_date' => 'required|date',
            'payment_method' => 'required|string|max:255',
            'installments' => 'nullable|integer|min:1',
            'installment_value' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variation_id' => 'required|exists:product_variations,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($data, $id) {
            $purchase = Purchase::findOrFail($id);
            $total = collect($data['items'])->sum(fn($item) => $item['quantity'] * $item['unit_price']);

            $purchase->update([
                'supplier_name' => $data['supplier_name'] ?? null,
                'purchase_date' => now()->parse($data['purchase_date'])->format('Y-m-d'),
                'payment_method' => $data['payment_method'],
                'installments' => $data['installments'] ?? 1,
                'installment_value' => $data['installment_value'] ?? $total,
                'total' => $total,
            ]);

            // Remover itens e parcelas antigas
            $purchase->items()->delete();
            Installment::where('purchase_id', $purchase->id)->delete();

            foreach ($data['items'] as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'product_variation_id' => $item['product_variation_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            // Recriar parcelas
            $this->createInstallments($purchase, $total, $data);
        });

        return redirect()->route('admin.purchases.index')->with('success', 'Compra atualizada com sucesso.');
    }

    public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);

        DB::transaction(function () use ($purchase) {
            $purchase->items()->delete();
            Installment::where('purchase_id', $purchase->id)->delete();
            $purchase->delete();
        });

        return redirect()->route('admin.purchases.index')->with('success', 'Compra removida com sucesso.');
    }

    /**
     * Criar parcelas para a compra
     */
    private function createInstallments($purchase, $total, $data)
    {
        $installments = $data['installments'] ?? 1;
        $purchaseDate = Carbon::parse($data['purchase_date']);

        $firstDueDate = $purchaseDate->copy()->addDays(30);

        $installmentAmount = $total / $installments;

        $totalInstallments = ($installments - 1) * $installmentAmount;
        $lastInstallmentAmount = $total - $totalInstallments;

        for ($i = 1; $i <= $installments; $i++) {
            $dueDate = $firstDueDate->copy()->addMonths($i - 1);
            $amount = ($i === $installments) ? $lastInstallmentAmount : $installmentAmount;

            Installment::create([
                'sale_id' => null,
                'purchase_id' => $purchase->id,
                'due_date' => $dueDate->format('Y-m-d'),
                'amount' => round($amount, 2),
                'status' => 'pending', // pending, paid, overdue
            ]);
        }
    }
}
