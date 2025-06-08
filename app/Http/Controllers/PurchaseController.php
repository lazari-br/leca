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
    public function index(Request $request)
    {
        $query = Purchase::with('items');

        // Aplicar filtros se existirem
        if ($request->filled('start_date')) {
            $query->where('purchase_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('purchase_date', '<=', $request->end_date);
        }

        if ($request->filled('supplier')) {
            $query->where('supplier_name', 'like', '%' . $request->supplier . '%');
        }

        $purchases = $query->orderBy('purchase_date', 'desc')->get();
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
                'purchase_date' => $data['purchase_date'],
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
                'purchase_date' => $data['purchase_date'],
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
     * Exportar relatório total de compras
     */
    public function exportTotal(Request $request)
    {
        $query = Purchase::with(['items.product', 'items.variation']);

        // Aplicar filtros se existirem
        if ($request->filled('start_date')) {
            $query->where('purchase_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('purchase_date', '<=', $request->end_date);
        }

        if ($request->filled('supplier')) {
            $query->where('supplier_name', 'like', '%' . $request->supplier . '%');
        }

        $purchases = $query->orderBy('purchase_date', 'desc')->get();

        $csvData = [];
        $csvData[] = ['Data', 'Produto', 'Variação', 'Quantidade', 'Fornecedor', 'Valor Total', 'Número de Parcelas'];

        foreach ($purchases as $purchase) {
            foreach ($purchase->items as $item) {
                $csvData[] = [
                    Carbon::parse($purchase->purchase_date)->format('d/m/Y'),
                    $item->product->name ?? 'N/A',
                    ($item->variation->size ?? 'N/A') . ' - ' . ($item->variation->color ?? 'N/A'),
                    $item->quantity,
                    $purchase->supplier_name ?? 'N/A',
                    'R$ ' . number_format($purchase->total, 2, ',', '.'),
                    $purchase->installments ?? 1
                ];
            }
        }

        return $this->downloadCsv($csvData, 'relatorio_total_compras_' . date('Y-m-d') . '.csv');
    }

    /**
     * Exportar relatório mensal de compras (por parcelas)
     */
    public function exportMonthly(Request $request)
    {
        $query = Installment::with(['purchase.items.product', 'purchase.items.variation'])
            ->whereNotNull('purchase_id');

        // Aplicar filtros se existirem
        if ($request->filled('start_date')) {
            $query->where('due_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('due_date', '<=', $request->end_date);
        }

        $installments = $query->orderBy('due_date', 'desc')->get();

        $csvData = [];
        $csvData[] = ['Data Vencimento', 'Produto', 'Variação', 'Quantidade', 'Fornecedor', 'Valor da Parcela'];

        foreach ($installments as $installment) {
            $purchase = $installment->purchase;
            if ($purchase) {
                foreach ($purchase->items as $item) {
                    $csvData[] = [
                        Carbon::parse($installment->due_date)->format('d/m/Y'),
                        $item->product->name ?? 'N/A',
                        ($item->variation->size ?? 'N/A') . ' - ' . ($item->variation->color ?? 'N/A'),
                        $item->quantity,
                        $purchase->supplier_name ?? 'N/A',
                        'R$ ' . number_format($installment->amount, 2, ',', '.')
                    ];
                }
            }
        }

        return $this->downloadCsv($csvData, 'relatorio_mensal_compras_' . date('Y-m-d') . '.csv');
    }

    /**
     * Helper para download de CSV
     */
    private function downloadCsv($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Adicionar BOM para UTF-8
            fwrite($file, "\xEF\xBB\xBF");

            foreach ($data as $row) {
                fputcsv($file, $row, ';'); // Usar ponto e vírgula para Excel brasileiro
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Criar parcelas para a compra
     */
    private function createInstallments($purchase, $total, $data)
    {
        $installments = $data['installments'] ?? 1;
        $purchaseDate = Carbon::parse($data['purchase_date']);

        // Para compras, vamos usar a data da compra como base para as parcelas
        // Primeira parcela vence 30 dias após a compra
        $firstDueDate = $purchaseDate->copy()->addDays(30);

        // Calcular valor de cada parcela
        $installmentAmount = $total / $installments;

        // Ajustar última parcela para compensar arredondamentos
        $totalInstallments = ($installments - 1) * $installmentAmount;
        $lastInstallmentAmount = $total - $totalInstallments;

        for ($i = 1; $i <= $installments; $i++) {
            // Calcular data de vencimento (primeira parcela 30 dias após compra, demais mensalmente)
            $dueDate = $firstDueDate->copy()->addMonths($i - 1);

            // Valor da parcela (última parcela pode ter valor diferente por causa do arredondamento)
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
