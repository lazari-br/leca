<?php

namespace App\Http\Controllers;

use App\Models\ProductVariation;
use App\Models\User;
use App\Models\UserType;
use App\Models\Product;
use App\Models\SellerStock;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SellerController extends Controller
{

    public function index()
    {
        // Buscar tipo "vendedor" (id = 2 ou name = 'vendedor')
        $sellerType = UserType::where('id', 2)
            ->orWhere('name', 'vendedor')
            ->first();

        if (!$sellerType) {
            return redirect()->back()->with('error', 'Tipo de usuário "vendedor" não encontrado.');
        }

        // Buscar vendedores com métricas dos últimos 30 dias
        $sellers = User::where('user_type_id', $sellerType->id)
            ->with(['type'])
            ->get()
            ->map(function ($seller) {
                // Calcular métricas dos últimos 30 dias
                $thirtyDaysAgo = Carbon::now()->subDays(30);

                // Vendas dos últimos 30 dias
                $recentSales = Sale::where('seller_id', $seller->id)
                    ->where('sale_date', '>=', $thirtyDaysAgo)
                    ->get();

                // Quantidade total de produtos vendidos
                $totalQuantity = DB::table('sale_items')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->where('sales.seller_id', $seller->id)
                    ->where('sales.sale_date', '>=', $thirtyDaysAgo)
                    ->sum('sale_items.quantity');

                // Valor total de vendas
                $totalSales = (float) $recentSales->sum('total');

                $seller->products_sold_30_days = $totalQuantity ?? 0;
                $seller->sales_value_30_days = $totalSales ?? 0;

                return $seller;
            });

        return view('admin.sellers.index', compact('sellers'));
    }

    public function create()
    {
        return view('admin.sellers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ], [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'Digite um email válido.',
            'email.unique' => 'Este email já está cadastrado.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
        ]);

        // Buscar tipo "vendedor"
        $sellerType = UserType::where('id', 2)
            ->orWhere('name', 'vendedor')
            ->first();

        if (!$sellerType) {
            return redirect()->back()->with('error', 'Tipo de usuário "vendedor" não encontrado.');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type_id' => $sellerType->id,
        ]);

        return redirect()->route('admin.sellers.index')
            ->with('success', 'Vendedor cadastrado com sucesso!');
    }

    public function edit($id)
    {
        $seller = User::findOrFail($id);

        $sellerType = UserType::where('id', 2)
            ->orWhere('name', 'vendedor')
            ->first();

        if (!$sellerType || $seller->user_type_id != $sellerType->id) {
            return redirect()->back()->with('error', 'Usuário não é um vendedor.');
        }

        $products = Product::with(['category'])->where('active', 1)->orderBy('name')->get();

        $sellerStocks = SellerStock::where('seller_id', $seller->id)
            ->with(['product'])
            ->orderBy('created_at', 'desc')
            ->get(); // Remover o ->keyBy('product_id')

        $variations = \App\Models\ProductVariation::with(['product'])
            ->where('active', 1)
            ->where('stock', '>', 0)
            ->orderBy('code')
            ->get();

        return view('admin.sellers.edit', compact('seller', 'products', 'sellerStocks', 'variations'));
    }

    public function update(Request $request, $id)
    {
        $seller = User::findOrFail($id);

        // Verificar se é vendedor
        $sellerType = UserType::where('id', 2)
            ->orWhere('name', 'vendedor')
            ->first();

        if (!$sellerType || $seller->user_type_id != $sellerType->id) {
            return redirect()->back()->with('error', 'Usuário não é um vendedor.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $seller->id,
            'commission' => 'nullable|numeric|min:0|max:100',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8';
        }

        $request->validate($rules, [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'Digite um email válido.',
            'email.unique' => 'Este email já está cadastrado.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'commission.numeric' => 'A comissão deve ser um número.',
            'commission.min' => 'A comissão deve ser no mínimo 0%.',
            'commission.max' => 'A comissão deve ser no máximo 100%.',
        ]);

        // Atualizar dados básicos
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'commission' => $request->commission ? (float) $request->commission : null,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $seller->update($updateData);

        // Atualizar estoque
        if ($request->has('stocks')) {
            foreach ($request->stocks as $productId => $quantity) {
                $quantity = (int) $quantity;

                if ($quantity > 0) {
                    // Verificar se o produto existe
                    $product = Product::find($productId);
                    if (!$product) {
                        continue;
                    }

                    // Criar ou atualizar estoque
                    SellerStock::updateOrCreate(
                        [
                            'seller_id' => $seller->id,
                            'product_id' => $productId,
                        ],
                        [
                            'quantity' => $quantity,
                        ]
                    );
                } else {
                    // Remover estoque se quantidade for 0
                    SellerStock::where('seller_id', $seller->id)
                        ->where('product_id', $productId)
                        ->delete();
                }
            }
        }

        return redirect()->route('admin.sellers.index')
            ->with('success', 'Vendedor atualizado com sucesso!');
    }

    public function updatePost(Request $request, $id)
    {
        $seller = User::findOrFail($id);

        $sellerType = UserType::where('id', 2)
            ->orWhere('name', 'vendedor')
            ->first();

        if (!$sellerType || $seller->user_type_id != $sellerType->id) {
            return redirect()->back()->with('error', 'Usuário não é um vendedor.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $seller->id,
            'commission' => 'nullable|numeric|min:0|max:100',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8';
        }

        $request->validate($rules, [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'Digite um email válido.',
            'email.unique' => 'Este email já está cadastrado.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'commission.numeric' => 'A comissão deve ser um número.',
            'commission.min' => 'A comissão deve ser no mínimo 0%.',
            'commission.max' => 'A comissão deve ser no máximo 100%.',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'commission' => $request->commission ? (float) $request->commission : null,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $seller->update($updateData);

        if ($request->has('stocks')) {
            foreach ($request->stocks as $productId => $quantity) {
                $quantity = (int) $quantity;

                // Apenas processar se quantidade > 0
                if ($quantity > 0) {
                    $product = Product::find($productId);
                    if ($product) {
                        SellerStock::updateOrCreate(
                            [
                                'seller_id' => $seller->id,
                                'product_id' => $productId,
                            ],
                            [
                                'quantity' => $quantity,
                            ]
                        );
                    }
                }
                // Se quantidade = 0, remover do estoque
                elseif ($quantity == 0) {
                    SellerStock::where('seller_id', $seller->id)
                        ->where('product_id', $productId)
                        ->delete();
                }
                // Ignorar valores null/negativos
            }
        }

        return redirect()->route('admin.sellers.index')
            ->with('success', 'Vendedor atualizado com sucesso via POST!');
    }

    public function addStock(Request $request, $id)
    {
        $seller = User::findOrFail($id);

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'product_variation_id' => 'required|exists:product_variations,id',
        ]);

        $variation = ProductVariation::where('product_id', $request->product_id)
            ->where('stock', '>=', $request->quantity)
            ->first();

        if (!$variation) {
            return response()->json([
                'success' => false,
                'message' => 'Estoque insuficiente para este produto.'
            ]);
        }

        $sellerStock = SellerStock::where([
            'seller_id' => $seller->id,
            'product_id' => $request->product_id,
            'product_variation_id' => $request->product_variation_id,
        ])
            ->first();

        !empty($sellerStock) ?
            $sellerStock->increment('quantity', $request->quantity) :
            SellerStock::create([
                'seller_id' => $seller->id,
                'product_id' => $request->product_id,
                'product_variation_id' => $request->product_variation_id,
                'quantity' => $request->quantity,
            ]);

        $sellerStock = $sellerStock->fresh()->load('product');
        return response()->json([
            'success' => true,
            'message' => $request->quantity . ' unidades adicionadas ao estoque!',
            'stock' => [
                'product_id' => $sellerStock->product_id,
                'product_name' => $sellerStock->product->name,
                'quantity' => $sellerStock->quantity
            ]
        ]);
    }

    public function removeStock(Request $request, $id)
    {
        $seller = User::findOrFail($id);

        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $deleted = SellerStock::where('seller_id', $seller->id)
            ->where('product_id', $request->product_id)
            ->delete();

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Produto removido do estoque!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Produto não encontrado no estoque.'
            ]);
        }
    }

    public function destroy($id)
    {
        $seller = User::findOrFail($id);

        // Verificar se é vendedor
        $sellerType = UserType::where('id', 2)
            ->orWhere('name', 'vendedor')
            ->first();

        if (!$sellerType || $seller->user_type_id != $sellerType->id) {
            return redirect()->back()->with('error', 'Usuário não é um vendedor.');
        }

        // Verificar se tem vendas associadas
        $hasSales = Sale::where('seller_id', $seller->id)->exists();

        if ($hasSales) {
            return redirect()->back()->with('error', 'Não é possível excluir vendedor que possui vendas registradas.');
        }

        // Remover estoque do vendedor
        SellerStock::where('seller_id', $seller->id)->delete();

        // Excluir vendedor
        $seller->delete();

        return redirect()->route('admin.sellers.index')
            ->with('success', 'Vendedor excluído com sucesso!');
    }
}
