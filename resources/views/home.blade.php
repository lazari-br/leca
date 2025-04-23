<!-- resources/views/home.blade.php -->
@extends('layouts.app')

@section('title', 'Leca Pijamas e Moda Fitness - Home')

@section('content')
    <!-- Hero Banner -->
    <div class="bg-gradient-to-r from-pink-400 to-pink-600 text-white rounded-lg mb-8">
        <div class="container mx-auto px-4 py-16">
            <div class="max-w-xl">
                <h1 class="text-4xl font-bold mb-4">Leca Pijamas e Moda Fitness</h1>
                <p class="text-xl mb-8">Conforto e estilo para todos os momentos da sua vida.</p>
                <div class="flex space-x-4">
                    <a href="{{ route('product.category', 'fitness') }}" class="bg-white text-pink-500 px-6 py-3 rounded-lg font-bold hover:bg-gray-100 transition-colors">Moda Fitness</a>
                    <a href="{{ route('product.category', 'pijamas') }}" class="bg-transparent border-2 border-white text-white px-6 py-3 rounded-lg font-bold hover:bg-white hover:text-pink-500 transition-colors">Pijamas</a>
                </div>
            </div>
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
@endsection