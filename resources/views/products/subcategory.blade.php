@extends('layouts.app')

@section('title', ucfirst($subcategory) . ' - ' . $category->name . ' - Leca Moda Fitness')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ ucfirst($subcategory) }}</h1>
        <p class="text-gray-600">Produtos da categoria {{ $category->name }}</p>

        <!-- Breadcrumbs -->
        <div class="flex items-center text-sm text-gray-500 mt-4">
            <a href="{{ route('home') }}" class="hover:text-pink-500">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('product.category', $category->slug) }}" class="hover:text-pink-500">{{ $category->name }}</a>
            <span class="mx-2">/</span>
            <span>{{ ucfirst($subcategory) }}</span>
        </div>
    </div>

    <!-- Back to Category -->
    <div class="mb-6">
        <a href="{{ route('product.category', $category->slug) }}"
           class="inline-flex items-center text-pink-500 hover:text-pink-600 font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Voltar para {{ $category->name }}
        </a>
    </div>

    <!-- Filter and Sort Options -->
    <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-600">
                Mostrando {{ $products->count() }} produto{{ $products->count() !== 1 ? 's' : '' }} em {{ ucfirst($subcategory) }}
            </span>
        </div>

        <div class="flex items-center space-x-4">
            <!-- Sort dropdown -->
            <select onchange="window.location.href = this.value" class="text-sm border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                <option value="{{ route('product.subcategory', [$category->slug, $subcategory]) }}">Ordenar por</option>
                <option value="{{ route('product.subcategory', [$category->slug, $subcategory]) }}?sort=name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Nome A-Z</option>
                <option value="{{ route('product.subcategory', [$category->slug, $subcategory]) }}?sort=name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Nome Z-A</option>
                <option value="{{ route('product.subcategory', [$category->slug, $subcategory]) }}?sort=price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Menor preço</option>
                <option value="{{ route('product.subcategory', [$category->slug, $subcategory]) }}?sort=price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Maior preço</option>
                <option value="{{ route('product.subcategory', [$category->slug, $subcategory]) }}?sort=newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Mais recentes</option>
            </select>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($products as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                <a href="{{ route('product.show', $product->slug) }}">
                    <div class="aspect-w-1 aspect-h-1 h-48 overflow-hidden">
                        @if($product->images->count() > 0)
                            <img src="{{ $product->images->where('is_main', true)->first() ? $product->images->where('is_main', true)->first()->image_url : $product->images->first()->image_url }}"
                                 alt="{{ $product->name }}"
                                 class="w-full h-full object-cover">
                        @elseif($product->image)
                            <img src="{{ asset($product->image) }}"
                                 alt="{{ $product->name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-500">Sem imagem</span>
                            </div>
                        @endif
                    </div>
                </a>

                <div class="p-4">
                    <a href="{{ route('product.show', $product->slug) }}">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 hover:text-pink-500 transition-colors">{{ $product->name }}</h3>
                    </a>

                    <!-- SKU Information -->
                    <div class="text-sm text-gray-600 mb-2">
                        @if($product->variations->where('active', true)->count() > 0)
                            @php
                                $availableVariations = $product->variations->where('active', true);
                                $totalStock = $availableVariations->sum('stock');
                                $sizes = $availableVariations->pluck('size')->unique();
                                $colors = $availableVariations->pluck('color')->unique()->filter();
                                $sampleSku = $availableVariations->first();
                            @endphp

                                <!-- Sample SKU -->
                            @if($sampleSku)
                                <div class="mb-2">
                                    <span class="text-xs font-mono bg-gray-100 px-2 py-1 rounded">{{ $sampleSku->code }}</span>
                                </div>
                            @endif

                            <div class="flex flex-wrap gap-1 mb-2">
                                @foreach($sizes->take(4) as $size)
                                    <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">{{ $size }}</span>
                                @endforeach
                                @if($sizes->count() > 4)
                                    <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">+{{ $sizes->count() - 4 }}</span>
                                @endif
                            </div>

                            @if($colors->count() > 0)
                                <div class="flex gap-1 mb-2">
                                    @foreach($colors->take(5) as $color)
                                        <div class="w-4 h-4 rounded-full border border-gray-300" style="background-color: {{ $color }}" title="{{ $color }}"></div>
                                    @endforeach
                                    @if($colors->count() > 5)
                                        <span class="text-xs text-gray-500 flex items-center">+{{ $colors->count() - 5 }}</span>
                                    @endif
                                </div>
                            @endif

                            <div class="text-xs text-gray-500">
                                {{ $availableVariations->count() }} {{ $availableVariations->count() == 1 ? 'SKU' : 'SKUs' }}
                                @if($totalStock > 0)
                                    • {{ $totalStock }} em estoque
                                    @if($totalStock <= 5)
                                        <span class="text-amber-600">• Últimas unidades</span>
                                    @endif
                                @else
                                    • <span class="text-red-500">Sem estoque</span>
                                @endif
                            </div>
                        @else
                            <span class="text-red-500 text-xs">Nenhum SKU disponível</span>
                        @endif
                    </div>

                    <div class="text-xl font-bold text-pink-500 mb-3">
                        R$ {{ number_format($product->price, 2, ',', '.') }}
                    </div>

                    <!-- Stock status button -->
                    @php
                        $hasStock = $product->variations->where('active', true)->sum('stock') > 0;
                    @endphp

                    <a href="{{ route('product.show', $product->slug) }}"
                       class="block w-full text-center py-2 rounded-md transition-colors text-sm font-medium {{ $hasStock ? 'bg-pink-500 hover:bg-pink-600 text-white' : 'bg-gray-300 text-gray-600 cursor-not-allowed' }}">
                        {{ $hasStock ? 'Ver Produto' : 'Produto Esgotado' }}
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500">
                <div class="mb-4">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8V5a2 2 0 00-2-2H6a2 2 0 00-2 2v1m0 7h16"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Nenhum produto encontrado</h3>
                <p class="text-gray-600">Não há produtos disponíveis em "{{ ucfirst($subcategory) }}" no momento.</p>
                <div class="mt-4 space-x-4">
                    <a href="{{ route('product.category', $category->slug) }}" class="text-pink-500 hover:text-pink-600 font-medium">
                        ← Ver todos os produtos de {{ $category->name }}
                    </a>
                    <span class="text-gray-400">|</span>
                    <a href="{{ route('home') }}" class="text-pink-500 hover:text-pink-600 font-medium">
                        Voltar ao início
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(method_exists($products, 'links'))
        <div class="mt-8">
            {{ $products->links() }}
        </div>
    @endif
@endsection
