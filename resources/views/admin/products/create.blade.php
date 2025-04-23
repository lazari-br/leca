@extends('layouts.app')

@section('title', 'Adicionar Produto - Leca Pijamas e Moda Fitness')

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
        <h1 class="text-2xl font-bold text-gray-800 mt-2">Adicionar Novo Produto</h1>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nome e Código -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome do Produto</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Código do Produto</label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                    @error('code')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Preço -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Preço (R$)</label>
                    <input type="number" name="price" id="price" value="{{ old('price') }}" required step="0.01" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                    @error('price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="flex items-center mt-6">
                    <input type="checkbox" name="active" id="active" value="1" {{ old('active') ? 'checked' : '' }}
                        class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                    <label for="active" class="ml-2 block text-sm text-gray-700">Produto Ativo</label>
                </div>
            </div>

            <!-- Categoria e Subcategoria -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                    <select name="category_id" id="category_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                        <option value="">Selecione uma categoria</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="subcategory" class="block text-sm font-medium text-gray-700 mb-1">Subcategoria</label>
                    <input type="text" name="subcategory" id="subcategory" value="{{ old('subcategory') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                    @error('subcategory')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Descrição -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                <textarea name="description" id="description" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Imagem -->
            <div class="mt-6">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Imagem do Produto</label>
                <input type="file" name="image" id="image" accept="image/*"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                @error('image')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tamanhos -->
            <div class="mt-6" x-data="{ selectedSizes: [] }">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tamanhos Disponíveis</label>
                <div class="sizes-container">
                    @foreach(['PP', 'P', 'M', 'G', 'GG', 'XG', 'XXG', 'XXXG', 'Único'] as $size)
                        <label class="size-option">
                            <input type="checkbox" name="sizes[]" value="{{ $size }}" 
                                {{ in_array($size, old('sizes', [])) ? 'checked' : '' }}
                                @click="selectedSizes.includes('{{ $size }}') ? selectedSizes = selectedSizes.filter(s => s !== '{{ $size }}') : selectedSizes.push('{{ $size }}')">
                            {{ $size }}
                        </label>
                    @endforeach
                </div>
                @error('sizes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

                <!-- Estoque por tamanho -->
                <div class="mt-4" x-show="selectedSizes.length > 0">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Estoque por tamanho</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <template x-for="size in selectedSizes" :key="size">
                            <div>
                                <label :for="'stock_' + size" class="block text-sm font-medium text-gray-700 mb-1" x-text="size"></label>
                                <input type="number" :name="'stock_' + size" :id="'stock_' + size" value="0" min="0" step="1"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Cores -->
            <div class="mt-6" x-data="{ selectedColors: [], showColorInput: false, newColor: '' }">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cores Disponíveis</label>
                <div class="colors-container">
                    @foreach(['Preto', 'Branco', 'Cinza', 'Vermelho', 'Rosa', 'Azul', 'Verde', 'Amarelo', 'Roxo', 'Laranja'] as $color)
                        <label class="color-option">
                            <input type="checkbox" name="colors[]" value="{{ $color }}" 
                                {{ in_array($color, old('colors', [])) ? 'checked' : '' }}
                                @click="selectedColors.includes('{{ $color }}') ? selectedColors = selectedColors.filter(c => c !== '{{ $color }}') : selectedColors.push('{{ $color }}')">
                            {{ $color }}
                        </label>
                    @endforeach
                    <button type="button" @click="showColorInput = !showColorInput" 
                        class="py-2 px-4 border border-gray-300 rounded-md hover:bg-gray-50">
                        + Cor personalizada
                    </button>
                </div>

                <!-- Input para cor personalizada -->
                <div class="mt-2" x-show="showColorInput">
                    <div class="flex">
                        <input type="text" x-model="newColor" placeholder="Digite o nome da cor"
                            class="w-full px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                        <button type="button" @click="if(newColor) { selectedColors.push(newColor); document.querySelector('form').insertAdjacentHTML('beforeend', `<input type='hidden' name='colors[]' value='${newColor}'>`); newColor = ''; }"
                            class="px-4 py-2 bg-pink-500 text-white rounded-r-md hover:bg-pink-600">
                            Adicionar
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-colors">
                    Salvar Produto
                </button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection