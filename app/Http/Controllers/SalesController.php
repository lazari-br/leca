<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Models\Sale;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Installment;
use App\Services\SalesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function __construct(public SalesService $salesService) {}

    public function index(Request $request)
    {
        $sales = $this->salesService->getSales($request);
        return view('admin.sales.index', compact('sales'));
    }

    public function create()
    {
        $query = Product::with(['variations' => function($query) {
            $query->where('active', true);
        }])
            ->where('active', true);

        if (auth()->user()->type->name !== 'admin') {
            $query->whereHas('sellerStocks');
        }

        $products = $query->get();

        return view('admin.sales.create', compact('products'));
    }

    public function store(StoreSaleRequest $request)
    {
        $this->salesService->createSale($request->all());
        return redirect()->route('admin.sales.index')->with('success', 'Venda registrada com sucesso.');
    }

    public function edit($id)
    {
        $sale = Sale::with(['items.variation.product'])->findOrFail($id);
        $products = Product::with(['variations' => function($query) {
            $query->where('active', true);
        }])->where('active', true)->get();

        return view('admin.sales.edit', compact('sale', 'products'));
    }

    public function update(UpdateSaleRequest $request, $id)
    {
        $this->salesService->updateSale($id, $request->all());
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

    public function getProductVariations($productId)
    {
        $variations = ProductVariation::where('product_id', $productId)
            ->where('active', true)
            ->where('stock', '>', 0)
            ->get();

        return response()->json($variations);
    }

    public function getVariationByCode($code)
    {
        $variation = ProductVariation::with('product')
            ->where('code', $code)
            ->where('active', true)
            ->first();

        if (!$variation) {
            return response()->json(['error' => 'SKU não encontrado'], 404);
        }

        return response()->json([
            'id' => $variation->id,
            'code' => $variation->code,
            'product_name' => $variation->product->name,
            'size' => $variation->size,
            'color' => $variation->color,
            'stock' => $variation->stock,
            'price' => $variation->product->price,
        ]);
    }
}
