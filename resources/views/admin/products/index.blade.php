@extends('layouts.app')

@section('title', 'Gerenciar Produtos - Leca Moda Fitness')

@section('content')
    <div class="container mx-auto px-4">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Gerenciar Produtos</h1>
            <a href="{{ route('admin.products.create') }}" class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-4 rounded transition-colors">
                Adicionar Produto
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Imagem
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nome
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            SKUs
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Preço
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Categoria
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estoque Total
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ações
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->images->count() > 0)
                                    <img src="{{ $product->images->where('is_main', true)->first()->image_url ?? $product->images->first()->image_url }}"
                                         alt="{{ $product->name }}"
                                         class="w-16 h-16 object-cover rounded">
                                @elseif($product->image)
                                    <img src="{{ asset($product->image) }}"
                                         alt="{{ $product->name }}"
                                         class="w-16 h-16 object-cover rounded">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 flex items-center justify-center rounded">
                                        <span class="text-gray-500 text-xs">Sem imagem</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                @if($product->subcategory)
                                    <div class="text-sm text-gray-500">{{ ucfirst($product->subcategory) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    @if($product->variations->count() > 0)
                                        <div class="space-y-1">
                                            @foreach($product->variations->take(3) as $variation)
                                                <div class="text-xs bg-gray-100 px-2 py-1 rounded">
                                                    <strong>{{ $variation->code }}</strong> - {{ $variation->size }}
                                                    @if($variation->color)
                                                        ({{ $variation->color }})
                                                    @endif
                                                    - Estoque: {{ $variation->stock }}
                                                </div>
                                            @endforeach
                                            @if($product->variations->count() > 3)
                                                <div class="text-xs text-gray-500">
                                                    +{{ $product->variations->count() - 3 }} mais...
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-red-500 text-xs">Nenhum SKU</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">R$ {{ number_format($product->price, 2, ',', '.') }}</div>
                                <div class="text-xs text-gray-500">Compra: R$ {{ number_format($product->purchase_price, 2, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $product->category->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $totalStock = $product->variations->sum('stock');
                                @endphp
                                <div class="text-sm text-gray-900">
                                    {{ $totalStock }}
                                    @if($totalStock == 0)
                                        <span class="text-red-500 text-xs">(Sem estoque)</span>
                                    @elseif($totalStock <= 5)
                                        <span class="text-yellow-500 text-xs">(Baixo)</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Ativo
                                        </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Inativo
                                        </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="text-pink-600 hover:text-pink-900 mr-3">Editar</a>
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Tem certeza que deseja excluir este produto? Todos os SKUs serão removidos.')">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                Nenhum produto cadastrado.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>
@endsection
