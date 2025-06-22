<?php

namespace App\Services;

use App\Models\Installment;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getCashFlow(Carbon $startDate, Carbon $endDate)
    {
        // Garantir que a data final não seja anterior à inicial
        if ($endDate->lt($startDate)) {
            $endDate = $startDate->copy()->addDays(30);
        }

        // Buscar parcelas de vendas (ENTRADAS) - todas as parcelas, não só as pagas
        $salesInstallments = Installment::select(
            DB::raw('DATE(due_date) as date'),
            DB::raw('SUM(amount) as total'),
            DB::raw('SUM(commission_value) as commission_total'),
            DB::raw('COUNT(*) as count')
        )
            ->whereNotNull('sale_id')
            ->whereBetween('due_date', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(due_date)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Buscar parcelas de compras (SAÍDAS) - todas as parcelas, não só as pagas
        $purchaseInstallments = Installment::select(
            DB::raw('DATE(due_date) as date'),
            DB::raw('SUM(amount) as total'),
            DB::raw('COUNT(*) as count')
        )
            ->whereNotNull('purchase_id')
            ->whereBetween('due_date', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(due_date)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Gerar array de datas para o período - APENAS COM DADOS
        $cashFlowData = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');

            $income = isset($salesInstallments[$dateStr]) ? $salesInstallments[$dateStr]->total : 0;
            $expenses = isset($purchaseInstallments[$dateStr]) ? $purchaseInstallments[$dateStr]->total : 0;
            $commission = isset($salesInstallments[$dateStr]) ? $salesInstallments[$dateStr]->commission_total : 0;

            // Só adicionar se tiver movimento (entrada, saída ou comissão)
            if ($income > 0 || $expenses > 0 || $commission > 0) {
                $cashFlowData[] = [
                    'period' => $dateStr,
                    'income' => $income,
                    'expenses' => $expenses,
                    'commission' => $commission
                ];
            }

            $currentDate->addDay();
        }

        // Se não houver dados, criar pelo menos uma estrutura vazia para evitar erros
        if (empty($cashFlowData)) {
            $cashFlowData[] = [
                'period' => $startDate->format('Y-m-d'),
                'income' => 0,
                'expenses' => 0,
                'commission' => 0
            ];
        }

        return $cashFlowData;
    }

    public function getProductBusinessAnalysis(Collection $trade): array
    {
        $totalTrade = $trade->sum('total');
        $totalTransactions = $trade->count();
        $averageTicket = $totalTransactions > 0 ? $totalTrade / $totalTransactions : 0;

        // Vendas por método de pagamento
        $paymentMethods = $trade->groupBy('payment_method')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('total')
                ];
            });

        // Top produtos vendidos
        $topProducts = $trade->flatMap->items
            ->groupBy('product_id')
            ->map(function ($group) {
                return [
                    'product_name' => $group->first()->product->name,
                    'quantity' => $group->sum('quantity'),
                    'total' => $group->sum(function ($item) {
                        return $item->quantity * $item->unit_price;
                    })
                ];
            })
            ->sortByDesc('total')
            ->take(10);

        return [
            'totalTrade' => $totalTrade,
            'totalTransactions' => $totalTransactions,
            'averageTicket' => $averageTicket,
            'paymentMethods' => $paymentMethods,
            'topProducts' => $topProducts,
        ];
    }
}
