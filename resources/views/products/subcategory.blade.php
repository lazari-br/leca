<!-- resources/views/products/subcategory.blade.php -->
@extends('layouts.app')

@section('title', ucfirst($subcategory) . ' - ' . $category->name . ' - Leca Pijamas e Moda Fitness')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ ucfirst($subcategory) }}</h1>
        <div class="flex items-center text-sm text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-pink-500">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('product.category', $category->slug) }}" class="hover:text-pink-500">{{ $category->name }}</a>
            <span class="mx-2">/</span>
            <span>{{ ucfirst($subcategory) }}</span>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($products as $product)
            <x-product-card :product="$product" />
        @empty
            <div class="col-span-full text-center py-12 text-gray-500">
                <p>Nenhum produto encontrado nesta subcategoria.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $products->links() }}
    </div>
@endsection