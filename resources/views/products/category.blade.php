<!-- resources/views/products/category.blade.php -->
@extends('layouts.app')

@section('title', $category->name . ' - Leca Moda Fitness')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $category->name }}</h1>
        @if($category->description)
            <p class="text-gray-600">{{ $category->description }}</p>
        @endif
    </div>

    <!-- Subcategories Tabs -->
    @php
        $subcategories = $products->pluck('subcategory')->unique();
    @endphp

    @if($subcategories->count() > 1)
        <div class="flex overflow-x-auto pb-2 mb-6 scrollbar-hide" x-data="{ activeSubcategory: 'all' }">
            <button
                @click="activeSubcategory = 'all'"
                :class="{ 'bg-pink-500 text-white': activeSubcategory === 'all', 'bg-gray-200 text-gray-700': activeSubcategory !== 'all' }"
                class="px-4 py-2 rounded-full mr-2 whitespace-nowrap font-medium text-sm transition-colors"
            >
                Todos
            </button>

            @foreach($subcategories as $subcategory)
                <a
                    href="{{ route('product.subcategory', [$category->slug, $subcategory]) }}"
                    class="px-4 py-2 rounded-full mr-2 whitespace-nowrap font-medium text-sm transition-colors bg-gray-200 text-gray-700 hover:bg-gray-300"
                >
                    {{ ucfirst($subcategory) }}
                </a>
            @endforeach
        </div>
    @endif

    <!-- Products Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($products as $product)
            <x-product-card :product="$product" />
        @empty
            <div class="col-span-full text-center py-12 text-gray-500">
                <p>Nenhum produto encontrado nesta categoria.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $products->links() }}
    </div>
@endsection
