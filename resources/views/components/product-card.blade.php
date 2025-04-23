<!-- resources/views/components/product-card.blade.php -->
@props(['product'])

<div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
    <a href="{{ route('product.show', $product->slug) }}">
        <div class="aspect-w-1 aspect-h-1 overflow-hidden">
            @if($product->images->count() > 0)
                <img src="{{ asset($product->images->where('is_main', true)->first() ? $product->images->where('is_main', true)->first()->image_path : $product->images->first()->image_path) }}" 
                     alt="{{ $product->name }}" 
                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
            @elseif($product->image)
                <img src="{{ asset($product->image) }}" 
                     alt="{{ $product->name }}" 
                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
            @else
                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                    <span class="text-gray-500">Sem imagem</span>
                </div>
            @endif
        </div>
        <div class="p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-1 truncate">{{ $product->name }}</h3>
            <p class="text-gray-600 text-xs mb-2">{{ ucfirst($product->subcategory) }}</p>
            <div class="flex justify-between items-center">
                <span class="text-pink-500 font-bold">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                <div class="text-xs text-gray-500">
                    @if(count($product->getAvailableSizesAttribute()) > 0)
                        {{ implode(', ', $product->getAvailableSizesAttribute()) }}
                    @endif
                </div>
            </div>
        </div>
    </a>
</div>