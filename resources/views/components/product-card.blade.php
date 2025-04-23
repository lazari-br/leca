<!-- resources/views/components/product-card.blade.php -->
@props(['product'])

<div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
    <a href="{{ route('product.show', $product->slug) }}">
        @if($product->image)
            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-64 object-cover">
        @else
            <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                <span class="text-gray-500">Sem imagem</span>
            </div>
        @endif
    </a>
    <div class="p-4">
        <h3 class="font-bold text-lg mb-2">{{ $product->name }}</h3>
        <p class="text-gray-600 text-sm mb-4">{{ ucfirst($product->subcategory) }}</p>
        <div class="flex justify-between items-center">
            <span class="text-pink-500 font-bold">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
            <a href="{{ route('product.show', $product->slug) }}" class="text-pink-500 hover:text-pink-600 font-medium">Ver detalhes</a>
        </div>
    </div>
</div>