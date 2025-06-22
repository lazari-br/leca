<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Installment;
use App\Services\DashboardService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct(public DashboardService $dashboardService) {}

    public function index(): ?View
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $monthlySales = Sale::whereBetween('sale_date', [$currentMonth, $endOfMonth])->sum('total');
        $monthlyPurchases = Purchase::whereBetween('purchase_date', [$currentMonth, $endOfMonth])->sum('total');
        $pendingInstallments = Installment::where('status', 'pending')->count();

        return view('admin.dashboards.index', compact(
            'monthlySales',
            'monthlyPurchases',
            'pendingInstallments'
        ));
    }

    public function cashFlow(Request $request): ?View
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        $cashFlowData = $this->dashboardService->getCashFlow($startDate, $endDate);
        $totalIncome = array_sum(array_column($cashFlowData, 'income'));
        $totalExpenses = array_sum(array_column($cashFlowData, 'expenses'));
        $totalCommission = array_sum(array_column($cashFlowData, 'commission'));

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
            'totalCommission',
            'salesCount',
            'purchasesCount',
            'startDate',
            'endDate'
        ))->with([
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d')
        ]);
    }

    public function salesReport(Request $request): ?View
    {
        // Período padrão (último mês)
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $sales = Sale::with(['items.product', 'items.variation'])
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->orderBy('sale_date', 'desc')
            ->get();

        $salesAnalysis = $this->dashboardService->getProductBusinessAnalysis($sales);
        $totalTrade = $salesAnalysis['totalTrade'];
        $totalTransactions = $salesAnalysis['totalTransactions'];
        $averageTicket = $salesAnalysis['averageTicket'];
        $paymentMethods = $salesAnalysis['paymentMethods'];
        $topProducts = $salesAnalysis['topProducts'];

        return view('admin.dashboards.sales-report', compact(
            'sales',
            'totalTrade',
            'totalTransactions',
            'averageTicket',
            'paymentMethods',
            'topProducts',
            'startDate',
            'endDate'
        ));
    }

    public function purchasesReport(Request $request): ?View
    {
        // Período padrão (último mês)
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $purchases = Purchase::with(['items.product', 'items.variation'])
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->orderBy('purchase_date', 'desc')
            ->get();

        $purchasesAnalysis = $this->dashboardService->getProductBusinessAnalysis($purchases);
        $totalTrade = $purchasesAnalysis['totalTrade'];
        $totalTransactions = $purchasesAnalysis['totalTransactions'];
        $averageTicket = $purchasesAnalysis['averageTicket'];
        $paymentMethods = $purchasesAnalysis['paymentMethods'];
        $topProducts = $purchasesAnalysis['topProducts'];

        return view('admin.dashboards.purchases-report', compact(
            'purchases',
            'totalTrade',
            'totalTransactions',
            'averageTicket',
            'paymentMethods',
            'topProducts',
            'startDate',
            'endDate'
        ));
    }
}
