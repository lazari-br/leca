<!-- resources/views/products/show.blade.php -->
@extends('layouts.app')

@section('title', $product->name . ' - Leca Pijamas e Moda Fitness')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <!-- Breadcrumbs -->
    <nav class="flex mb-6 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-pink-500">Home</a>
        <span class="mx-2">/</span>
        <a href="{{ route('product.category', $product->category->slug) }}" class="hover:text-pink-500">{{ $product->category->name }}</a>
        <span class="mx-2">/</span>
        <a href="{{ route('product.subcategory', [$product->category->slug, $product->subcategory]) }}" class="hover:text-pink-500">{{ ucfirst($product->subcategory) }}</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Product Image -->
        <div>
            @if($product->image)
                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-auto rounded-lg shadow-md">
            @else
                <div class="w-full h-96 bg-gray-200 flex items-center justify-center rounded-lg">
                    <span class="text-gray-500">Sem imagem</span>
                </div>
            @endif
            
            <!-- Additional Images would go here -->
        </div>

        <!-- Product Details -->
        <div x-data="{ selectedSize: '', selectedColor: '' }">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $product->name }}</h1>
            
            <div class="flex items-center mb-4">
                <span class="text-gray-600 mr-2">Código:</span>
                <span class="text-gray-800">{{ $product->code }}</span>
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
                                @click="selectedSize = '{{ $size }}'" 
                                :class="{ 'bg-pink-500 text-white border-pink-500': selectedSize === '{{ $size }}', 'bg-white text-gray-800 border-gray-300 hover:border-pink-500': selectedSize !== '{{ $size }}' }"
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
                                @click="selectedColor = '{{ $color }}'" 
                                :class="{ 'ring-2 ring-pink-500 ring-offset-2': selectedColor === '{{ $color }}' }"
                                class="w-10 h-10 rounded-full border border-gray-300 transition-all"
                                style="background-color: {{ $color }};"
                            >
                                <span class="sr-only">{{ $color }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <!-- WhatsApp Button -->
            <div class="mt-8">
                <a 
                    href="https://wa.me/5500000000000?text=Olá! Tenho interesse no produto {{ $product->name }}%20(Código: {{ $product->code }})" 
                    target="_blank" 
                    class="flex items-center justify-center bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                    </svg>
                    Comprar pelo WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Related Products -->
<div class="mt-12">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Produtos Relacionados</h2>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($product->category->products->where('id', '!=', $product->id)->take(4) as $relatedProduct)
            <x-product-card :product="$relatedProduct" />
        @endforeach
    </div>
</div>
@endsection