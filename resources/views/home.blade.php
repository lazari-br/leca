<!-- resources/views/home.blade.php -->
 
@extends('layouts.app')

@section('title', 'Leca Pijamas e Moda Fitness - Home')

@section('content')
<!-- Swiper CSS -->
<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"
/>

    <!-- Hero Banner -->
    <div class="container mx-auto px-4 py-6">
    <div class="swiper mySwiper">
        <div class="swiper-wrapper">
        @foreach($categories as $category)
            @foreach($category->products->take(8) as $product)
            <div class="swiper-slide">
                <div class="p-4 bg-white rounded shadow text-center">
                    <img src="{{ $product->slug }}" alt="{{ $product->name }}" class="w-full h-40 object-cover mb-2 rounded">
                    <h2 class="text-lg font-semibold">{{ $product->name }}</h2>
                </div>
                </div>
            @endforeach
        @endforeach
        </div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-pagination"></div>
  </div>
</div>

 <!-- Categories Section -->
 @foreach($categories as $category)
        @if($category->products->count() > 0)
            <section class="mb-12">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $category->name }}</h2>
                    <a href="{{ route('product.category', $category->slug) }}" class="text-pink-500 hover:text-pink-600 font-medium">Ver todos</a>
                </div>

                <!-- Subcategories Tabs -->
                @php
                    $subcategories = $category->products->pluck('subcategory')->unique();
                @endphp
                
                @if($subcategories->count() > 1)
                    <div class="flex overflow-x-auto pb-2 mb-6 scrollbar-hide" x-data="{ activeTab: '{{ $subcategories->first() }}' }">
                        <button 
                            @click="activeTab = 'all'" 
                            :class="{ 'bg-pink-500 text-white': activeTab === 'all', 'bg-gray-200 text-gray-700': activeTab !== 'all' }"
                            class="px-4 py-2 rounded-full mr-2 whitespace-nowrap font-medium text-sm transition-colors"
                        >
                            Todos
                        </button>
                        
                        @foreach($subcategories as $subcategory)
                            <button 
                                @click="activeTab = '{{ $subcategory }}'" 
                                :class="{ 'bg-pink-500 text-white': activeTab === '{{ $subcategory }}', 'bg-gray-200 text-gray-700': activeTab !== '{{ $subcategory }}' }"
                                class="px-4 py-2 rounded-full mr-2 whitespace-nowrap font-medium text-sm transition-colors"
                            >
                                {{ ucfirst($subcategory) }}
                            </button>
                        @endforeach
                    </div>
                @endif

                <!-- Products Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" x-data="{ activeTab: 'all' }">
                    @foreach($category->products->take(8) as $product)
                        <div 
                            x-show="activeTab === 'all' || activeTab === '{{ $product->subcategory }}'"
                            class="transition-opacity duration-300"
                        >
                            <x-product-card :product="$product" />
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    @endforeach

    <!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- Inicialização do Swiper -->
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