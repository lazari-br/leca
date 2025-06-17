@extends('layouts.app')

@section('title', 'Editar Produto - Leca Pijamas e Moda Fitness')

@section('content')
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <a href="{{ route('admin.products.index') }}" class="text-pink-500 hover:text-pink-700">
                &larr; Voltar para lista de produtos
            </a>
            <h1 class="text-2xl font-bold text-gray-800 mt-2">Editar Produto</h1>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form id="productEditForm" action="{{ route('admin.products.update.post', $product->id) }}" method="POST" enctype="multipart/form-data">
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

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Nome -->
                    <div class="md:col-span-3">
                        <label for="product_name" class="block text-sm font-medium text-gray-700 mb-1">Nome do Produto</label>
                        <input type="text" name="name" id="product_name" value="{{ old('name', $product->name) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                        @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Código -->
                    <div>
                        <label for="product_code" class="block text-sm font-medium text-gray-700 mb-1">Código do Produto</label>
                        <input type="text" name="code" id="product_code" value="{{ old('code', $product->code) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                        @error('code')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Preço -->
                    <div>
                        <label for="product_price" class="block text-sm font-medium text-gray-700 mb-1">Preço de Venda (R$)</label>
                        <input type="number" name="price" id="product_price" value="{{ old('price', $product->price) }}" required step="0.01" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                        @error('price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Preço de Compra -->
                    <div>
                        <label for="product_purchase_price" class="block text-sm font-medium text-gray-700 mb-1">Preço de Compra (R$)</label>
                        <input type="number" name="purchase_price" id="product_purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" required step="0.01" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                        @error('purchase_price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Status -->
                <div class="mt-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="active" id="product_active" value="1" {{ old('active', $product->active) ? 'checked' : '' }}
                        class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                        <label for="product_active" class="ml-2 block text-sm text-gray-700">Produto Ativo</label>
                    </div>
                </div>

                <!-- Categoria e Subcategoria -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="product_category_id" class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                        <select name="category_id" id="product_category_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                            <option value="" disabled>Selecione uma categoria</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="product_subcategory" class="block text-sm font-medium text-gray-700 mb-1">Subcategoria</label>
                        <input type="text" name="subcategory" id="product_subcategory" value="{{ old('subcategory', $product->subcategory) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                        @error('subcategory')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Descrição -->
                <div class="mt-6">
                    <label for="product_description" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                    <textarea name="description" id="product_description" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Imagens -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Imagens do Produto</label>

                    <!-- Exibir imagens existentes -->
                    @if($product->images->count() > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-4">
                            @foreach($product->images as $image)
                                <div class="relative border rounded-lg p-1">
                                    <div style="width: 100%; height: 150px; background: #f0f0f0; border-radius: 5px; overflow: hidden;">
                                        <img src="{{ $image->image_url }}"
                                             alt="{{ $product->name }}"
                                             style="width: 100%; height: 100%; object-fit: cover;"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                        <div style="display: none; padding: 20px; text-align: center; color: #666; font-size: 12px;">
                                            Imagem não encontrada<br>
                                            <small>{{ $image->image_path }}</small><br>
                                            <small>URL: {{ $image->image_url }}</small>
                                        </div>
                                    </div>

                                    @if($image->is_main)
                                        <div style="position: absolute; top: 5px; left: 5px; background: #e91e63; color: white; padding: 2px 8px; border-radius: 10px; font-size: 10px;">
                                            Principal
                                        </div>
                                    @endif

                                    <div class="mt-2 flex justify-between text-xs">
                                        @if(!$image->is_main)
                                            <form action="{{ route('admin.products.images.main', [$product->id, $image->id]) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" style="color: #1976d2; text-decoration: none; border: none; background: none; cursor: pointer;">
                                                    Definir principal
                                                </button>
                                            </form>
                                        @else
                                            <span style="color: #666;">Imagem principal</span>
                                        @endif

                                        <form action="{{ route('admin.products.images.delete', [$product->id, $image->id]) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="color: #d32f2f; text-decoration: none; border: none; background: none; cursor: pointer;" onclick="return confirm('Excluir imagem?')">
                                                Excluir
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 mb-4">Este produto ainda não possui imagens.</p>
                    @endif

                    <!-- Upload de novas imagens -->
                    <div class="mt-4">
                        <label for="product_images" class="block text-sm font-medium text-gray-700 mb-1">Adicionar Novas Imagens</label>
                        <input type="file" name="images[]" id="product_images" accept="image/*" multiple
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                        <p class="text-sm text-gray-500 mt-1">Selecione uma ou mais imagens para adicionar ao produto</p>
                        @error('images.*')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Tamanhos -->
                @php
                    $existingSizes = $product->variations->pluck('size')->unique()->values()->toArray();
                    $hasExistingVariations = count($existingSizes) > 0;
                @endphp
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tamanhos Disponíveis
                        @if(!$hasExistingVariations)
                            <span class="text-red-500">*</span>
                        @endif
                    </label>
                    @if($hasExistingVariations)
                        <p class="text-sm text-gray-600 mb-2">Este produto já possui variações. Você pode adicionar/remover tamanhos.</p>
                    @endif
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                        @foreach(['PP', 'P', 'M', 'G', 'GG', 'XG', 'XXG', 'XXXG', 'Único'] as $size)
                            <label style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; cursor: pointer;">
                                <input type="checkbox" name="sizes[]" value="{{ $size }}"
                                       {{ in_array($size, old('sizes', $existingSizes)) ? 'checked' : '' }}
                                       style="margin-right: 0.5rem;">
                                {{ $size }}
                            </label>
                        @endforeach
                    </div>
                    @error('sizes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

                    <!-- Estoque por tamanho -->
                    @php
                        $stockBySize = $product->variations->groupBy('size')->map(function($variations) {
                            return $variations->first()->stock;
                        });
                    @endphp
                    <div class="mt-4">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Estoque por tamanho</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach(['PP', 'P', 'M', 'G', 'GG', 'XG', 'XXG', 'XXXG', 'Único'] as $size)
                                @if(in_array($size, $existingSizes))
                                    <div>
                                        <label for="stock_{{ $size }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $size }}</label>
                                        <input type="number" name="stock_{{ $size }}" id="stock_{{ $size }}"
                                               value="{{ old('stock_' . $size, $stockBySize->get($size, 0)) }}"
                                               min="0" step="1"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Cores -->
                @php
                    $existingColors = $product->variations->whereNotNull('color')->pluck('color')->unique()->values()->toArray();
                @endphp
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cores Disponíveis</label>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                        @foreach($colors as $color)
                            <label style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; cursor: pointer;">
                                <input type="checkbox" name="colors[]" value="{{ $color['hex'] }}"
                                       {{ in_array($color['hex'], old('colors', $existingColors)) ? 'checked' : '' }}
                                       style="margin-right: 0.5rem;">
                                {{ $color['name'] }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit" form="productEditForm" class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-colors">
                        Atualizar Produto
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        console.log('✅ Formulário com ID único carregado!');

        // Garantir que o submit seja do formulário correto
        document.getElementById('productEditForm').addEventListener('submit', function(e) {
            console.log('🎯 Formulário CORRETO sendo enviado:', this.id);
        });
    </script>
@endsection
