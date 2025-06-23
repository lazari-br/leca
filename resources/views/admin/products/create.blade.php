@extends('layouts.app')

@section('title', 'Adicionar Produto - Leca Moda Fitness')

@section('styles')
    <style>
        .sku-item {
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            background-color: #f8fafc;
        }
        .sku-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .remove-sku-btn {
            background-color: #ef4444;
            color: white;
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            cursor: pointer;
            font-size: 0.875rem;
        }
        .remove-sku-btn:hover {
            background-color: #dc2626;
        }
        .add-sku-btn {
            background-color: #10b981;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            cursor: pointer;
            margin-bottom: 1rem;
        }
        .add-sku-btn:hover {
            background-color: #059669;
        }
    </style>
@endsection

@section('content')
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <a href="{{ route('admin.products.index') }}" class="text-pink-500 hover:text-pink-700">
                &larr; Voltar para lista de produtos
            </a>
            <h1 class="text-2xl font-bold text-gray-800 mt-2">Adicionar Novo Produto</h1>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
                @csrf

                <!-- Mostrar erros de validação -->
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- SEÇÃO 1: DADOS DO PRODUTO -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Informações do Produto</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nome -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome do Produto <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                            @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Preço -->
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Preço de Venda (R$) <span class="text-red-500">*</span></label>
                            <input type="number" name="price" id="price" value="{{ old('price') }}" required step="0.01" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                            @error('price')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Preço de Compra -->
                        <div>
                            <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-1">Preço de Compra (R$) <span class="text-red-500">*</span></label>
                            <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price') }}" required step="0.01" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                            @error('purchase_price')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Categoria -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Categoria <span class="text-red-500">*</span></label>
                            <select name="category_id" id="category_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                <option value="" disabled selected>Selecione uma categoria</option>
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

                        <!-- Subcategoria -->
                        <div>
                            <label for="subcategory" class="block text-sm font-medium text-gray-700 mb-1">Subcategoria</label>
                            <input type="text" name="subcategory" id="subcategory" value="{{ old('subcategory') }}"
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

                    <!-- Status -->
                    <div class="mt-6">
                        <div class="flex items-center">
                            <input type="checkbox" name="active" id="active" value="1" {{ old('active', true) ? 'checked' : '' }}
                            class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                            <label for="active" class="ml-2 block text-sm text-gray-700">Produto Ativo</label>
                        </div>
                    </div>

                    <!-- Imagens -->
                    <div class="mt-6">
                        <label for="images" class="block text-sm font-medium text-gray-700 mb-1">Imagens do Produto</label>
                        <p class="text-sm text-gray-500 mb-2">Você pode selecionar várias imagens. A primeira imagem será definida como a principal.</p>
                        <input type="file" name="images[]" id="images" accept="image/*" multiple
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                        @error('images.*')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- SEÇÃO 2: SKUs (VARIAÇÕES) -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">SKUs / Variações <span class="text-red-500">*</span></h2>
                        <button type="button" class="add-sku-btn" onclick="addSkuItem()">+ Adicionar SKU</button>
                    </div>

                    <div id="skus-container">
                        <!-- SKUs serão adicionados aqui via JavaScript -->
                    </div>

                    @error('skus')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-8">
                    <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-colors">
                        Salvar Produto
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let skuCounter = 0;
        const colors = @json($colors);

        function addSkuItem() {
            const container = document.getElementById('skus-container');
            const skuHtml = `
                <div class="sku-item" id="sku-${skuCounter}">
                    <div class="sku-header">
                        <h4 class="font-medium text-gray-700">SKU #${skuCounter + 1}</h4>
                        <button type="button" class="remove-sku-btn" onclick="removeSku(${skuCounter})">Remover</button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="sku_code_${skuCounter}" class="block text-sm font-medium text-gray-700 mb-1">Código SKU <span class="text-red-500">*</span></label>
                            <input type="text" name="skus[${skuCounter}][code]" id="sku_code_${skuCounter}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                        </div>

                        <div>
                            <label for="sku_size_${skuCounter}" class="block text-sm font-medium text-gray-700 mb-1">Tamanho <span class="text-red-500">*</span></label>
                            <select name="skus[${skuCounter}][size]" id="sku_size_${skuCounter}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                <option value="">Selecione</option>
                                <option value="PP">PP</option>
                                <option value="P">P</option>
                                <option value="M">M</option>
                                <option value="G">G</option>
                                <option value="GG">GG</option>
                                <option value="XG">XG</option>
                                <option value="XXG">XXG</option>
                                <option value="XXXG">XXXG</option>
                                <option value="Único">Único</option>
                            </select>
                        </div>

                        <div>
                            <label for="sku_color_${skuCounter}" class="block text-sm font-medium text-gray-700 mb-1">Cor</label>
                            <select name="skus[${skuCounter}][color]" id="sku_color_${skuCounter}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                <option value="">Sem cor específica</option>
                                ${colors.map(color => `<option value="${color.name}">${color.name}</option>`).join('')}
                            </select>
                        </div>

                        <div>
                            <label for="sku_stock_${skuCounter}" class="block text-sm font-medium text-gray-700 mb-1">Estoque <span class="text-red-500">*</span></label>
                            <input type="number" name="skus[${skuCounter}][stock]" id="sku_stock_${skuCounter}" required min="0" value="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                        </div>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', skuHtml);
            skuCounter++;
        }

        function removeSku(id) {
            const skuElement = document.getElementById(`sku-${id}`);
            if (skuElement) {
                skuElement.remove();
            }
        }

        // Adicionar um SKU inicial
        document.addEventListener('DOMContentLoaded', function() {
            addSkuItem();
        });

        // Validação antes do envio
        document.getElementById('productForm').addEventListener('submit', function(e) {
            const skusContainer = document.getElementById('skus-container');
            const skuItems = skusContainer.querySelectorAll('.sku-item');

            if (skuItems.length === 0) {
                e.preventDefault();
                alert('É necessário adicionar pelo menos um SKU.');
                return false;
            }
        });
    </script>
@endsection
