<div class="relative overflow-hidden rounded-lg">
    <a href="{{ route('product.show', $product->slug) }}">
        <div class="aspect-w-16 aspect-h-9">
            @if($product->images->count() > 0)
                <img src="{{ $product->images->where('is_main', true)->first()->image_url ?? $product->images->first()->image_url }}"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
            @elseif($product->image)
                <img src="{{ asset($product->image) }}"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
            @else
                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                    <span class="text-gray-500">Sem imagem</span>
                </div>
            @endif

            <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-70"></div>

            <div class="absolute bottom-0 left-0 p-4 text-white">
                <h3 class="text-xl font-bold mb-1">{{ $product->name }}</h3>
                <p class="text-sm mb-2">{{ ucfirst($product->subcategory) }}</p>
                <span class="bg-pink-500 px-2 py-1 rounded text-sm font-bold">
                    R$ {{ number_format($product->price, 2, ',', '.') }}
                </span>
            </div>
        </div>
    </a>
</div>
