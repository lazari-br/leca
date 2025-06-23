@extends('layouts.app')

@section('title', 'Leca Moda Fitness - Moda Fitness Feminina')

@section('content')
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- SEÇÃO 1: PRODUTOS EM DESTAQUE -->
    @if(isset($featuredProducts) && $featuredProducts->count() > 0)
        <section class="mb-12">
            <div class="container mx-auto px-4 py-6">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Produtos em Destaque</h1>
                    <p class="text-gray-600">Confira nossa seleção especial de moda fitness</p>
                </div>

                <div class="swiper mySwiper">
                    <div class="swiper-wrapper">
                        @foreach($featuredProducts as $product)
                            <div class="swiper-slide">
                                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                                    <a href="{{ route('product.show', $product->slug) }}">
                                        <div class="aspect-w-1 aspect-h-1 h-64 overflow-hidden">
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

                                        <div class="text-sm text-gray-600 mb-2">
                                            @if($product->variations->where('active', true)->count() > 0)
                                                @php
                                                    $availableVariations = $product->variations->where('active', true);
                                                    $totalStock = $availableVariations->sum('stock');
                                                    $sizes = $availableVariations->pluck('size')->unique();
                                                @endphp

                                                <div class="flex flex-wrap gap-1 mb-1">
                                                    @foreach($sizes->take(3) as $size)
                                                        <span class="inline-block bg-pink-100 text-pink-700 text-xs px-2 py-1 rounded">{{ $size }}</span>
                                                    @endforeach
                                                    @if($sizes->count() > 3)
                                                        <span class="inline-block bg-pink-100 text-pink-700 text-xs px-2 py-1 rounded">+{{ $sizes->count() - 3 }}</span>
                                                    @endif
                                                </div>

                                                <div class="text-xs text-gray-500">
                                                    {{ $availableVariations->count() }} {{ $availableVariations->count() == 1 ? 'variação' : 'variações' }}
                                                    @if($totalStock > 0)
                                                        • {{ $totalStock }} em estoque
                                                    @else
                                                        • <span class="text-red-500">Sem estoque</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-red-500 text-xs">Nenhuma variação disponível</span>
                                            @endif
                                        </div>

                                        <div class="text-xl font-bold text-pink-500 mb-3">
                                            R$ {{ number_format($product->price, 2, ',', '.') }}
                                        </div>

                                        <a href="{{ route('product.show', $product->slug) }}"
                                           class="block w-full bg-pink-500 hover:bg-pink-600 text-white text-center py-2 rounded-md transition-colors text-sm font-medium">
                                            Ver Produto
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </section>
    @endif

    <!-- SEÇÃO 2: CATÁLOGO POR CATEGORIAS (APENAS FITNESS) -->
    <div class="container mx-auto px-4">
        @foreach($categories as $category)
            @if($category->products->where('active', true)->count() > 0)
                @php
                    $activeProducts = $category->products->where('active', true);
                    $subcategories = $activeProducts->pluck('subcategory')->unique()->filter();
                @endphp

                <section class="mb-12" x-data="{ activeTab: 'all' }">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">{{ $category->name }}</h2>
                            @if($category->description)
                                <p class="text-gray-600 text-sm mt-1">{{ $category->description }}</p>
                            @endif
                        </div>
                        <a href="{{ route('product.category', $category->slug) }}" class="text-pink-500 hover:text-pink-600 font-medium">
                            Ver todo o catálogo →
                        </a>
                    </div>

                    @if($subcategories->count() > 1)
                        <div class="flex overflow-x-auto pb-2 mb-6 scrollbar-hide">
                            <button @click="activeTab = 'all'"
                                    :class="{ 'bg-pink-500 text-white': activeTab === 'all', 'bg-gray-200 text-gray-700': activeTab !== 'all' }"
                                    class="px-4 py-2 rounded-full mr-2 whitespace-nowrap font-medium text-sm transition-colors">
                                Todos ({{ $activeProducts->count() }})
                            </button>

                            @foreach($subcategories as $subcategory)
                                @php
                                    $subcategoryCount = $activeProducts->where('subcategory', $subcategory)->count();
                                @endphp
                                <button @click="activeTab = '{{ $subcategory }}'"
                                        :class="{ 'bg-pink-500 text-white': activeTab === '{{ $subcategory }}', 'bg-gray-200 text-gray-700': activeTab !== '{{ $subcategory }}' }"
                                        class="px-4 py-2 rounded-full mr-2 whitespace-nowrap font-medium text-sm transition-colors">
                                    {{ ucfirst($subcategory) }} ({{ $subcategoryCount }})
                                </button>
                            @endforeach
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach($activeProducts->take(8) as $product)
                            <div x-show="activeTab === 'all' || activeTab === '{{ $product->subcategory }}'"
                                 class="transition-opacity duration-300">
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

                                        <div class="text-sm text-gray-600 mb-2">
                                            @if($product->variations->where('active', true)->count() > 0)
                                                @php
                                                    $availableVariations = $product->variations->where('active', true);
                                                    $totalStock = $availableVariations->sum('stock');
                                                    $sizes = $availableVariations->pluck('size')->unique();
                                                    $colors = $availableVariations->pluck('color')->unique()->filter();
                                                @endphp

                                                <div class="flex flex-wrap gap-1 mb-2">
                                                    @foreach($sizes->take(3) as $size)
                                                        <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">{{ $size }}</span>
                                                    @endforeach
                                                    @if($sizes->count() > 3)
                                                        <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">+{{ $sizes->count() - 3 }}</span>
                                                    @endif
                                                </div>

                                                @if($colors->count() > 0)
                                                    <div class="flex gap-1 mb-2">
                                                        @foreach($colors->take(4) as $color)
                                                            <div class="w-4 h-4 rounded-full border border-gray-300" style="background-color: {{ $color }}" title="{{ $color }}"></div>
                                                        @endforeach
                                                        @if($colors->count() > 4)
                                                            <span class="text-xs text-gray-500">+{{ $colors->count() - 4 }}</span>
                                                        @endif
                                                    </div>
                                                @endif

                                                <div class="text-xs text-gray-500">
                                                    {{ $availableVariations->count() }} {{ $availableVariations->count() == 1 ? 'SKU' : 'SKUs' }} disponível{{ $availableVariations->count() == 1 ? '' : 'eis' }}
                                                    @if($totalStock > 0)
                                                        • {{ $totalStock }} em estoque
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

                                        <a href="{{ route('product.show', $product->slug) }}"
                                           class="block w-full bg-pink-500 hover:bg-pink-600 text-white text-center py-2 rounded-md transition-colors text-sm font-medium">
                                            Ver Produto
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($activeProducts->count() > 8)
                        <div class="text-center mt-8">
                            <a href="{{ route('product.category', $category->slug) }}"
                               class="inline-flex items-center px-6 py-3 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors">
                                Ver todos os {{ $activeProducts->count() }} produtos
                                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </a>
                        </div>
                    @endif
                </section>
            @endif
        @endforeach
    </div>

    <!-- SEÇÃO 3: CALL TO ACTION -->
    <section class="bg-pink-50 py-12 mt-12">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Vista-se com Estilo e Conforto</h2>
            <p class="text-gray-600 mb-8 max-w-2xl mx-auto">
                Descubra nossa coleção completa de moda fitness feminina. Peças pensadas para você que busca qualidade, conforto e estilo em cada treino.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                @if($categories->first())
                    <a href="{{ route('product.category', $categories->first()->slug) }}"
                       class="bg-pink-500 hover:bg-pink-600 text-white px-8 py-3 rounded-lg font-medium transition-colors">
                        Ver Catálogo Completo
                    </a>
                @endif
                <a href="https://wa.me/5511962163422?text=Olá! Gostaria de saber mais sobre os produtos da Leca Moda Fitness"
                   target="_blank"
                   class="bg-green-500 hover:bg-green-600 text-white px-8 py-3 rounded-lg font-medium transition-colors flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                    </svg>
                    Falar no WhatsApp
                </a>
            </div>
        </div>
    </section>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        const swiper = new Swiper(".mySwiper", {
            slidesPerView: "auto",
            spaceBetween: 20,
            breakpoints: {
                320: { slidesPerView: 1 },
                640: { slidesPerView: 2 },
                768: { slidesPerView: 3 },
                1024: { slidesPerView: 4 },
                1280: { slidesPerView: 5 },
            },
            loop: true,
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
        });
    </script>
@endsection
