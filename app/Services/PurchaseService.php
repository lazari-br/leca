<?php

namespace App\Services;

use App\Models\Installment;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function getPurchases(Request $request)
    {
        $query = Purchase::with('items')
            ->when($request->get('start_date'), function (Builder $query, $startDate) {
                $query->where('purchase_date', '>=', $startDate);
            })
            ->when($request->get('end_date'), function (Builder $query, $endDate) {
                $query->where('purchase_date', '<=', $endDate);
            })
            ->when($request->get('supplier'), function (Builder $query, $supplier) {
                $query->where('supplier_name', 'like', '%' . $supplier . '%');
            });

        return $query->orderBy('purchase_date', 'desc')->get();
    }

    public function createPurchase(array $data): void
    {
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

            $this->createInstallments($purchase, $total, $data);
        });
    }

    public function updatePurchase(int $id, array $data): void
    {
        DB::transaction(function () use ($data, $id) {
            $purchase = Purchase::findOrFail($id);
            $total = 0;

            if (isset($data['existing_items'])) {
                foreach ($data['existing_items'] as $existingItem) {
                    $total += $existingItem['quantity'] * $existingItem['unit_price'];
                }
            }

            if (isset($data['items'])) {
                foreach ($data['items'] as $newItem) {
                    $total += $newItem['quantity'] * $newItem['unit_price'];
                }
            }

            $purchase->update([
                'supplier_name' => $data['supplier_name'] ?? null,
                'purchase_date' => now()->parse($data['purchase_date'])->format('Y-m-d'),
                'payment_method' => $data['payment_method'],
                'installments' => $data['installments'] ?? 1,
                'installment_value' => $data['installment_value'] ?? ($total / ($data['installments'] ?? 1)),
                'total' => $total,
            ]);

            if (isset($data['items_to_delete']) && is_array($data['items_to_delete'])) {
                PurchaseItem::whereIn('id', $data['items_to_delete'])
                    ->where('purchase_id', $purchase->id)
                    ->delete();
            }

            if (isset($data['existing_items'])) {
                foreach ($data['existing_items'] as $itemData) {
                    if (isset($itemData['id'])) {
                        PurchaseItem::where('id', $itemData['id'])
                            ->where('purchase_id', $purchase->id)
                            ->update([
                                'quantity' => $itemData['quantity'],
                                'unit_price' => $itemData['unit_price'],
                            ]);
                    }
                }
            }

            if (isset($data['items'])) {
                foreach ($data['items'] as $newItem) {
                    if (isset($newItem['product_id'], $newItem['product_variation_id'], $newItem['quantity'], $newItem['unit_price'])) {
                        PurchaseItem::create([
                            'purchase_id' => $purchase->id,
                            'product_id' => $newItem['product_id'],
                            'product_variation_id' => $newItem['product_variation_id'],
                            'quantity' => $newItem['quantity'],
                            'unit_price' => $newItem['unit_price'],
                        ]);
                    }
                }
            }

            Installment::where('purchase_id', $purchase->id)->delete();
            $this->createInstallments($purchase, $total, $data);
        });
    }

    public function buildCsvTotalData(Collection $data): array
    {
        $csvData = [];
        $csvData[] = ['Data', 'Produto', 'Variação', 'Quantidade', 'Fornecedor', 'Valor Total', 'Número de Parcelas'];

        foreach ($data as $item) {
            foreach ($item->items as $item) {
                $csvData[] = [
                    Carbon::parse($item->purchase_date)->format('d/m/Y'),
                    $item->product->name ?? 'N/A',
                    ($item->variation->size ?? 'N/A') . ' - ' . ($item->variation->color ?? 'N/A'),
                    $item->quantity,
                    $item->supplier_name ?? 'N/A',
                    'R$ ' . number_format($item->total, 2, ',', '.'),
                    $item->installments ?? 1
                ];
            }
        }

        return $csvData;
    }

    public function buildCsvMonthlyData(Request $request): array
    {
        $installments = Installment::with(['purchase.items.product', 'purchase.items.variation'])
            ->whereNotNull('purchase_id')
            ->when($request->get('start_date'), function (Builder $query, $startDate) {
                $query->where('due_date', '>=', $startDate);
            })
            ->when($request->get('end_date'), function (Builder $query, $endDate) {
                $query->where('due_date', '<=', $endDate);
            })
            ->orderBy('due_date', 'desc')->get();

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

            return $csvData;
        }
    }

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
