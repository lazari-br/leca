<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\Installment;
use App\Services\PurchaseService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PurchaseController extends Controller
{
    public function __construct(public PurchaseService $purchaseService) {}

    public function index(Request $request): View
    {
        $purchases = $this->purchaseService->getPurchases($request);
        return view('admin.purchases.index', compact('purchases'));
    }

    public function create(): View
    {
        $products = Product::with('variations')->get();
        return view('admin.purchases.create', compact('products'));
    }

    public function store(StorePurchaseRequest $request): RedirectResponse
    {
        $this->purchaseService->createPurchase($request->all());
        return redirect()->route('admin.purchases.index')->with('success', 'Compra registrada com sucesso.');
    }

    public function edit($id): View
    {
        $purchase = Purchase::with('items')->findOrFail($id);
        $products = Product::with('variations')->get();
        return view('admin.purchases.edit', compact('purchase', 'products'));
    }

    public function update(UpdatePurchaseRequest $request, $id): RedirectResponse
    {
        $this->purchaseService->updatePurchase($id, $request->all());
        return redirect()->route('admin.purchases.index')->with('success', 'Compra atualizada com sucesso.');
    }

    public function destroy($id): RedirectResponse
    {
        $purchase = Purchase::findOrFail($id);

        DB::transaction(function () use ($purchase) {
            $purchase->items()->delete();
            Installment::where('purchase_id', $purchase->id)->delete();
            $purchase->delete();
        });

        return redirect()->route('admin.purchases.index')->with('success', 'Compra removida com sucesso.');
    }

    public function exportTotal(Request $request): StreamedResponse
    {
        $purchases = $this->purchaseService->getPurchases($request);
        $csvData = $this->purchaseService->buildCsvTotalData($purchases);

        return $this->downloadCsv($csvData, 'relatorio_total_compras_' . date('Y-m-d') . '.csv');
    }

    public function exportMonthly(Request $request): StreamedResponse
    {
        $csvData = $this->purchaseService->buildCsvMonthlyData($request);
        return $this->downloadCsv($csvData, 'relatorio_mensal_compras_' . date('Y-m-d') . '.csv');
    }

    private function downloadCsv($data, $filename): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");

            foreach ($data as $row) {
                fputcsv($file, $row, ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
