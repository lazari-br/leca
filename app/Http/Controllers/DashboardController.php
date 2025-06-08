<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Installment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Dados do mês atual
        $currentMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $monthlySales = Sale::whereBetween('sale_date', [$currentMonth, $endOfMonth])
            ->sum('total');

        $monthlyPurchases = Purchase::whereBetween('purchase_date', [$currentMonth, $endOfMonth])
            ->sum('total');

        $pendingInstallments = Installment::where('status', 'pending')
            ->count();

        return view('admin.dashboards.index', compact(
            'monthlySales',
            'monthlyPurchases',
            'pendingInstallments'
        ));
    }

    public function cashFlow(Request $request)
    {
        // Definir período padrão (últimos 30 dias)
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        // Validar datas
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        // Garantir que a data final não seja anterior à inicial
        if ($endDate->lt($startDate)) {
            $endDate = $startDate->copy()->addDays(30);
        }

        // Buscar parcelas de vendas (ENTRADAS) - todas as parcelas, não só as pagas
        $salesInstallments = Installment::select(
            DB::raw('DATE(due_date) as date'),
            DB::raw('SUM(amount) as total'),
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

            // Só adicionar se tiver movimento (entrada ou saída)
            if ($income > 0 || $expenses > 0) {
                $cashFlowData[] = [
                    'period' => $dateStr,
                    'income' => $income,
                    'expenses' => $expenses
                ];
            }

            $currentDate->addDay();
        }

        // Se não houver dados, criar pelo menos uma estrutura vazia para evitar erros
        if (empty($cashFlowData)) {
            $cashFlowData[] = [
                'period' => $startDate->format('Y-m-d'),
                'income' => 0,
                'expenses' => 0
            ];
        }

        // Calcular totais
        $totalIncome = array_sum(array_column($cashFlowData, 'income'));
        $totalExpenses = array_sum(array_column($cashFlowData, 'expenses'));

        // Contar parcelas no período
        $salesCount = Installment::whereNotNull('sale_id')
            ->whereBetween('due_date', [$startDate, $endDate])
            ->count();

        $purchasesCount = Installment::whereNotNull('purchase_id')
            ->whereBetween('due_date', [$startDate, $endDate])
            ->count();

        return view('admin.dashboards.cash-flow', compact(
            'cashFlowData',
            'totalIncome',
            'totalExpenses',
            'salesCount',
            'purchasesCount',
            'startDate',
            'endDate'
        ))->with([
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d')
        ]);
    }

    public function salesReport(Request $request)
    {
        // Período padrão (último mês)
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $sales = Sale::with(['items.product', 'items.variation'])
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->orderBy('sale_date', 'desc')
            ->get();

        // Estatísticas
        $totalSales = $sales->sum('total');
        $totalTransactions = $sales->count();
        $averageTicket = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

        // Vendas por método de pagamento
        $paymentMethods = $sales->groupBy('payment_method')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('total')
                ];
            });

        // Top produtos vendidos
        $topProducts = $sales->flatMap->items
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

        return view('admin.dashboards.sales-report', compact(
            'sales',
            'totalSales',
            'totalTransactions',
            'averageTicket',
            'paymentMethods',
            'topProducts',
            'startDate',
            'endDate'
        ));
    }

    public function purchasesReport(Request $request)
    {
        // Período padrão (último mês)
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $purchases = Purchase::with(['items.product', 'items.variation'])
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->orderBy('purchase_date', 'desc')
            ->get();

        // Estatísticas
        $totalPurchases = $purchases->sum('total');
        $totalTransactions = $purchases->count();
        $averageTicket = $totalTransactions > 0 ? $totalPurchases / $totalTransactions : 0;

        // Compras por método de pagamento
        $paymentMethods = $purchases->groupBy('payment_method')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('total')
                ];
            });

        // Top produtos comprados
        $topProducts = $purchases->flatMap->items
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

        // Top fornecedores
        $topSuppliers = $purchases->groupBy('supplier_name')
            ->map(function ($group) {
                return [
                    'supplier' => $group->first()->supplier_name ?? 'Não informado',
                    'count' => $group->count(),
                    'total' => $group->sum('total')
                ];
            })
            ->sortByDesc('total')
            ->take(10);

        return view('admin.dashboards.purchases-report', compact(
            'purchases',
            'totalPurchases',
            'totalTransactions',
            'averageTicket',
            'paymentMethods',
            'topProducts',
            'topSuppliers',
            'startDate',
            'endDate'
        ));
    }
}
