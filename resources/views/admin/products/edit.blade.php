@extends('layouts.app')

@section('title', 'Editar Produto - Leca Pijamas e Moda Fitness')

@section('styles')
<style>
    .sizes-container, .colors-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .size-option, .color-option {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        cursor: pointer;
    }
    .size-option input, .color-option input {
        margin-right: 0.5rem;
    }
</style>
@endsection

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.products.index') }}" class="text-pink-500 hover:text-pink-700">
            &larr; Voltar para lista de produtos
        </a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">Editar Produto</h1>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nome e Código -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome do Produto</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Código do Produto</label>
                    <input type="text" name="code" id="code" value="{{ old('code', $product->code) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                    @error('code')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Preço -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Preço (R$)</label>
                    <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" required step="0.01" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                    @error('price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="flex items-center mt-6">
                    <input type="checkbox" name="active" id="active" value="1" {{ old('active', $product->active) ? 'checked' : '' }}
                        class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                    <label for="active" class="ml-2 block text-sm