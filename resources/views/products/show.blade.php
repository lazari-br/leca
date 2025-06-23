@extends('layouts.app')

@section('title', $product->name . ' - Leca Moda Fitness')

@section('content')
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Breadcrumbs -->
        <nav class="flex mb-6 text-sm text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-pink-500">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('product.category', $product->category->slug) }}" class="hover:text-pink-500">{{ $product->category->name }}</a>
            @if($product->subcategory)
                <span class="mx-2">/</span>
                <a href="{{ route('product.subcategory', [$product->category->slug, $product->subcategory]) }}" class="hover:text-pink-500">{{ ucfirst($product->subcategory) }}</a>
            @endif
            <span class="mx-2">/</span>
            <span class="text-gray-800">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Product Images -->
            <div x-data="{ activeImage: 0, images: [] }" x-init="images = [
            @foreach($product->images as $index => $image)
                '{{ $image->image_url }}',
            @endforeach
            @if($product->images->count() == 0 && $product->image)
                '{{ asset($product->image) }}',
           @endif
]">
                <div class="mb-4">
                    <div class="aspect-w-1 aspect-h-1 overflow-hidden rounded-lg bg-gray-200">
                        <template x-if="images.length > 0">
                            <img :src="images[activeImage]" alt="{{ $product->name }}" class="object-cover w-full h-full">
                        </template>
                        <template x-if="images.length === 0">
                            <div class="w-full h-full flex items-center justify-center">
                                <span class="text-gray-500">Sem imagem</span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Thumbnails -->
                <div class="flex space-x-2 overflow-x-auto pb-2" x-show="images.length > 1">
                    <template x-for="(src, index) in images" :key="index">
                        <button
                            @click="activeImage = index"
                            :class="{ 'ring-2 ring-pink-500': activeImage === index }"
                            class="w-20 h-20 rounded-md overflow-hidden border flex-shrink-0">
                            <img :src="src" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        </button>
                    </template>
                </div>
            </div>

            <!-- Product Details -->
            <div x-data="{
            selectedSize: '',
            selectedColor: '',
            selectedVariation: null,
            availableStock: 0,
            selectedSku: '',
            variations: @js($product->variations->where('active', true)->values()),

            updateAvailableStock() {
                const variation = this.variations.find(v =>
                    v.size === this.selectedSize &&
                    (v.color === this.selectedColor || (!v.color && !this.selectedColor))
                );

                if (variation) {
                    this.selectedVariation = variation;
                    this.availableStock = variation.stock;
                    this.selectedSku = variation.code;
                } else {
                    this.selectedVariation = null;
                    this.availableStock = 0;
                    this.selectedSku = '';
                }
            },

            getAvailableColors() {
                if (!this.selectedSize) return @js($colors);
                return this.variations
                    .filter(v => v.size === this.selectedSize)
                    .map(v => v.color)
                    .filter(c => c !== null);
            },

            getAvailableSizes() {
                if (!this.selectedColor) return @js($sizes);
                return this.variations
                    .filter(v => v.color === this.selectedColor || (!v.color && !this.selectedColor))
                    .map(v => v.size);
            }
        }"
                 x-init="$watch('selectedSize', () => updateAvailableStock()); $watch('selectedColor', () => updateAvailableStock())">

                <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $product->name }}</h1>

                <!-- SKU Information -->
                <div class="mb-4">
                    <div class="flex items-center mb-2">
                        <span class="text-gray-600 mr-2">SKU Selecionado:</span>
                        <span class="text-gray-800 font-medium" x-text="selectedSku || 'Selecione tamanho/cor'"></span>
                    </div>

                    @if($product->variations->where('active', true)->count() > 0)
                        <div class="text-sm text-gray-600">
                            {{ $product->variations->where('active', true)->count() }} {{ $product->variations->where('active', true)->count() == 1 ? 'variação disponível' : 'variações disponíveis' }}
                        </div>
                    @else
                        <div class="text-sm text-red-600">
                            Nenhuma variação disponível
                        </div>
                    @endif
                </div>

                <div class="text-2xl font-bold text-pink-500 mb-6">
                    R$ {{ number_format($product->price, 2, ',', '.') }}
                </div>

                @if($product->description)
                    <div class="mb-6 text-gray-700">
                        {{ $product->description }}
                    </div>
                @endif

                <!-- Size Selection -->
                @if(count($sizes) > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2 text-gray-800">Tamanho</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($sizes as $size)
                                <button
                                    @click="selectedSize = selectedSize === '{{ $size }}' ? '' : '{{ $size }}'"
                                    :class="{
                                    'bg-pink-500 text-white border-pink-500': selectedSize === '{{ $size }}',
                                    'bg-white text-gray-800 border-gray-300 hover:border-pink-500': selectedSize !== '{{ $size }}',
                                    'opacity-50 cursor-not-allowed': !getAvailableSizes().includes('{{ $size }}')
                                }"
                                    :disabled="!getAvailableSizes().includes('{{ $size }}')"
                                    class="px-4 py-2 border-2 rounded-md font-medium transition-colors"
                                >
                                    {{ strtoupper($size) }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Color Selection -->
                @if(count($colors) > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2 text-gray-800">Cor</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($colors as $color)
                                <button
                                    @click="selectedColor = selectedColor === '{{ $color }}' ? '' : '{{ $color }}'"
                                    :class="{
                                    'ring-2 ring-pink-500 ring-offset-2': selectedColor === '{{ $color }}',
                                    'opacity-50': !getAvailableColors().includes('{{ $color }}')
                                }"
                                    :disabled="!getAvailableColors().includes('{{ $color }}')"
                                    class="w-10 h-10 rounded-full border border-gray-300 transition-all relative"
                                    style="background-color: {{ $color }};"
                                    :title="'{{ $color }}' + (getAvailableColors().includes('{{ $color }}') ? '' : ' - Indisponível')"
                                >
                                    <span class="sr-only">{{ $color }}</span>
                                    <template x-if="!getAvailableColors().includes('{{ $color }}')">
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <div class="w-6 h-0.5 bg-red-500 transform rotate-45"></div>
                                        </div>
                                    </template>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Stock Information -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Estoque disponível:</span>
                        <span class="text-sm font-bold"
                              :class="availableStock > 0 ? 'text-green-600' : 'text-red-600'"
                              x-text="selectedVariation ? availableStock + ' unidades' : 'Selecione tamanho/cor'">
                    </span>
                    </div>

                    <template x-if="selectedVariation && availableStock <= 5 && availableStock > 0">
                        <div class="text-xs text-amber-600 mt-1">⚠️ Últimas unidades!</div>
                    </template>

                    <template x-if="selectedVariation && availableStock === 0">
                        <div class="text-xs text-red-600 mt-1">❌ Fora de estoque</div>
                    </template>
                </div>

                <!-- All Available SKUs -->
                @if($product->variations->where('active', true)->count() > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">Todas as Variações Disponíveis</h3>
                        <div class="space-y-2 max-h-40 overflow-y-auto">
                            @foreach($product->variations->where('active', true) as $variation)
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded text-sm">
                                    <div class="flex items-center space-x-3">
                                        <span class="font-mono text-xs bg-gray-200 px-2 py-1 rounded">{{ $variation->code }}</span>
                                        <span class="font-medium">{{ $variation->size }}</span>
                                        @if($variation->color)
                                            <div class="flex items-center space-x-1">
                                                <div class="w-4 h-4 rounded border border-gray-300" style="background-color: {{ $variation->color }}"></div>
                                                <span class="text-gray-600">{{ $variation->color }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        @if($variation->stock > 0)
                                            <span class="text-green-600 font-medium">{{ $variation->stock }} em estoque</span>
                                        @else
                                            <span class="text-red-600">Sem estoque</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- WhatsApp Button -->
                <div class="mt-8">
                    <template x-if="selectedVariation && availableStock > 0">
                        <a
                            :href="`https://wa.me/5511962163422?text=Olá! Tenho interesse no produto {{ urlencode($product->name) }}%0ASKU: ${selectedSku}%0ATamanho: ${selectedSize}${selectedColor ? '%0ACor: ' + selectedColor : ''}%0APreço: R$ {{ number_format($product->price, 2, ',', '.') }}`"
                            target="_blank"
                            class="flex items-center justify-center bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition-colors w-full"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                            </svg>
                            Comprar pelo WhatsApp
                        </a>
                    </template>

                    <template x-if="!selectedVariation">
                        <div class="flex items-center justify-center bg-gray-400 text-white font-bold py-3 px-6 rounded-lg w-full cursor-not-allowed">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                            </svg>
                            Selecione tamanho e cor
                        </div>
                    </template>

                    <template x-if="selectedVariation && availableStock === 0">
                        <div class="flex items-center justify-center bg-red-500 text-white font-bold py-3 px-6 rounded-lg w-full cursor-not-allowed">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Produto Esgotado
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($product->category->products->where('id', '!=', $product->id)->where('active', true)->count() > 0)
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Produtos Relacionados</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($product->category->products->where('id', '!=', $product->id)->where('active', true)->take(4) as $relatedProduct)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <a href="{{ route('product.show', $relatedProduct->slug) }}">
                            <div class="aspect-w-1 aspect-h-1 h-48 overflow-hidden">
                                @if($relatedProduct->images->count() > 0)
                                    <img src="{{ $relatedProduct->images->where('is_main', true)->first() ? $relatedProduct->images->where('is_main', true)->first()->image_url : $relatedProduct->images->first()->image_url }}"
                                         alt="{{ $relatedProduct->name }}"
                                         class="w-full h-full object-cover">
                                @elseif($relatedProduct->image)
                                    <img src="{{ asset($relatedProduct->image) }}"
                                         alt="{{ $relatedProduct->name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-500">Sem imagem</span>
                                    </div>
                                @endif
                            </div>
                        </a>

                        <div class="p-4">
                            <a href="{{ route('product.show', $relatedProduct->slug) }}">
                                <h3 class="text-lg font-semibold text-gray-800 mb-2 hover:text-pink-500 transition-colors">{{ $relatedProduct->name }}</h3>
                            </a>

                            <div class="text-sm text-gray-600 mb-2">
                                @if($relatedProduct->variations->where('active', true)->count() > 0)
                                    {{ $relatedProduct->variations->where('active', true)->count() }} SKUs disponíveis
                                @else
                                    <span class="text-red-500">Nenhum SKU disponível</span>
                                @endif
                            </div>

                            <div class="text-xl font-bold text-pink-500 mb-3">
                                R$ {{ number_format($relatedProduct->price, 2, ',', '.') }}
                            </div>

                            <a href="{{ route('product.show', $relatedProduct->slug) }}"
                               class="block w-full bg-pink-500 hover:bg-pink-600 text-white text-center py-2 rounded-md transition-colors text-sm font-medium">
                                Ver Produto
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection
