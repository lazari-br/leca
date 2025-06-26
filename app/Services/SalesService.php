<?php

namespace App\Services;

use App\Models\Installment;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesService
{
    public function getSales(Request $request)
    {
        $userType = auth()->user()->user_type;
        $query = Sale::with(['items', 'seller'])
            ->when($request->get('start_date'), function (Builder $query, $startDate) {
                $query->where('sale_date', '>=', $startDate);
            })
            ->when($request->get('end_date'), function (Builder $query, $endDate) {
                $query->where('sale_date', '>=', $endDate);
            })
            ->when($request->get('customer'), function (Builder $query, $customer) {
                $query->where('customer_name', '>=', $customer);
            })
            ->when($request->get('seller') && $userType !== 'vendedor', function (Builder $query, $seller) {
                $query->whereHas('seller', fn($q) => $q->where('name', 'like', '%' . $seller . '%'));
            });

        if ($userType === 'vendedor') {
            $query->where('seller_id', auth()->user()->id);
        }

        return $query->orderBy('sale_date', 'desc')->get();
    }

    public function createSale(array $data): void
    {
        DB::transaction(function () use ($data) {
            $total = collect($data['items'])->sum(fn($item) => $item['quantity'] * $item['unit_price']);

            $commissionValue = 0;
            $sellerId = null;
            if (auth()->user()->type->name === 'vendedor') {
                $commissionValue = $total * (auth()->user()->commission / 100);
                $sellerId = auth()->id();
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
                'seller_id' => $sellerId
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

            $this->createInstallments($sale, $total, $data, $commissionValue);
        });
    }

    public function updateSale(int $id, array $data): void
    {
        DB::transaction(function () use ($data, $id) {
            $sale = Sale::findOrFail($id);

            // Calcular total dos itens (existentes + novos)
            $total = 0;

            // Somar itens existentes
            if (isset($data['existing_items'])) {
                foreach ($data['existing_items'] as $existingItem) {
                    $total += $existingItem['quantity'] * $existingItem['unit_price'];
                }
            }

            // Somar novos itens
            if (isset($data['items'])) {
                foreach ($data['items'] as $newItem) {
                    $total += $newItem['quantity'] * $newItem['unit_price'];
                }
            }

            // Calcular comissão
            $commissionValue = 0;
            if (auth()->user()->user_type === 'vendedor' && auth()->user()->commission) {
                $commissionValue = $total * (auth()->user()->commission / 100);
            }

            // Atualizar dados da venda
            $sale->update([
                'customer_name' => $data['customer_name'] ?? null,
                'payment_date' => now()->parse($data['payment_date'])->format('Y-m-d'),
                'sale_date' => now()->parse($data['sale_date'])->format('Y-m-d'),
                'payment_method' => $data['payment_method'],
                'installments' => $data['installments'] ?? 1,
                'installment_value' => $data['installment_value'] ?? ($total / ($data['installments'] ?? 1)),
                'commission_value' => $commissionValue,
                'total' => $total,
            ]);

            // 1. Deletar itens marcados para exclusão
            if (isset($data['items_to_delete']) && is_array($data['items_to_delete'])) {
                SaleItem::whereIn('id', $data['items_to_delete'])
                    ->where('sale_id', $sale->id)
                    ->delete();
            }

            // 2. Atualizar itens existentes
            if (isset($data['existing_items'])) {
                foreach ($data['existing_items'] as $itemData) {
                    if (isset($itemData['id'])) {
                        SaleItem::where('id', $itemData['id'])
                            ->where('sale_id', $sale->id)
                            ->update([
                                'quantity' => $itemData['quantity'],
                                'unit_price' => $itemData['unit_price'],
                                // product_id e product_variation_id não mudam para itens existentes
                            ]);
                    }
                }
            }

            // 3. Adicionar novos itens
            if (isset($data['items'])) {
                foreach ($data['items'] as $newItem) {
                    // Verificar se todos os campos obrigatórios estão presentes
                    if (isset($newItem['product_id'], $newItem['product_variation_id'], $newItem['quantity'], $newItem['unit_price'])) {
                        SaleItem::create([
                            'sale_id' => $sale->id,
                            'product_id' => $newItem['product_id'],
                            'product_variation_id' => $newItem['product_variation_id'],
                            'quantity' => $newItem['quantity'],
                            'unit_price' => $newItem['unit_price'],
                        ]);
                    }
                }
            }

            // 4. Recriar installments (sempre recria pois podem ter mudado)
            Installment::where('sale_id', $sale->id)->delete();
            $this->createInstallments($sale, $total, $data, $commissionValue);
        });
    }

    private function createInstallments($sale, $total, $data, $commissionValue = 0): void
    {
        $installments = $data['installments'] ?? 1;
        $paymentDate = Carbon::parse($data['payment_date']);
        $installmentAmount = $total / $installments;
        $commissionPerInstallment = $commissionValue / $installments;
        $totalInstallments = ($installments - 1) * $installmentAmount;
        $lastInstallmentAmount = $total - $totalInstallments;
        $totalCommissionInstallments = ($installments - 1) * $commissionPerInstallment;
        $lastCommissionAmount = $commissionValue - $totalCommissionInstallments;

        for ($i = 1; $i <= $installments; $i++) {
            $dueDate = $paymentDate->copy()->addMonths($i - 1);
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
