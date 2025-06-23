@extends('layouts.app')

@section('title', 'Editar Produto - Leca Moda Fitness')

@section('styles')
    <style>
        .sku-item {
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            background-color: #f8fafc;
        }
        .existing-sku-item {
            background-color: #fef3e2;
            border-color: #f59e0b;
        }
        .sku-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .remove-sku-btn, .delete-variation-btn {
            background-color: #ef4444;
            color: white;
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            cursor: pointer;
            font-size: 0.875rem;
        }
        .remove-sku-btn:hover, .delete-variation-btn:hover {
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
            <h1 class="text-2xl font-bold text-gray-800 mt-2">Editar Produto</h1>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form id="productEditForm" action="{{ route('admin.products.update.post', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Informações do Produto</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome do Produto <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                            @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Preço de Venda (R$) <span class="text-red-500">*</span></label>
                            <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" required step="0.01" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                            @error('price')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-1">Preço de Compra (R$) <span class="text-red-500">*</span></label>
                            <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" required step="0.01" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                            @error('purchase_price')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Categoria <span class="text-red-500">*</span></label>
                            <select name="category_id" id="category_id" required
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
                            <label for="subcategory" class="block text-sm font-medium text-gray-700 mb-1">Subcategoria</label>
                            <input type="text" name="subcategory" id="subcategory" value="{{ old('subcategory', $product->subcategory) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                            @error('subcategory')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                        <textarea name="description" id="description" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6">
                        <div class="flex items-center">
                            <input type="checkbox" name="active" id="active" value="1" {{ old('active', $product->active) ? 'checked' : '' }}
                            class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                            <label for="active" class="ml-2 block text-sm text-gray-700">Produto Ativo</label>
                        </div>
                    </div>
                </div>

                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Imagens do Produto</h2>

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
                                            <form id="setMainImageForm_{{ $image->id }}" action="{{ route('admin.products.images.main', [$product->id, $image->id]) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" style="color: #1976d2; text-decoration: none; border: none; background: none; cursor: pointer;">
                                                    Definir principal
                                                </button>
                                            </form>
                                        @else
                                            <span style="color: #666;">Imagem principal</span>
                                        @endif

                                        <button type="button" onclick="deleteImage({{ $product->id }}, {{ $image->id }})" style="color: #d32f2f; text-decoration: none; border: none; background: none; cursor: pointer;">
                                            Excluir
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 mb-4">Este produto ainda não possui imagens.</p>
                    @endif

                    <div class="mt-4">
                        <label for="images" class="block text-sm font-medium text-gray-700 mb-1">Adicionar Novas Imagens</label>
                        <input type="file" name="images[]" id="images" accept="image/*" multiple
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                        <p class="text-sm text-gray-500 mt-1">Selecione uma ou mais imagens para adicionar ao produto</p>
                        @error('images.*')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                @if($product->variations && $product->variations->count() > 0)
                    <div class="border-b border-gray-200 pb-6 mb-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">SKUs / Variações Existentes ({{ $product->variations->count() }})</h2>

                        @foreach($product->variations as $index => $variation)
                            <div class="sku-item existing-sku-item mb-4">
                                <div class="sku-header">
                                    <h4 class="font-medium text-gray-700">SKU: {{ $variation->code ?? 'Código não definido' }}</h4>
                                    <button type="button" onclick="deleteVariation({{ $product->id }}, {{ $variation->id }})" class="delete-variation-btn">
                                        Excluir
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <label for="update_code_{{ $variation->id }}" class="block text-sm font-medium text-gray-700 mb-1">Código SKU <span class="text-red-500">*</span></label>
                                        <input type="text"
                                               name="update_variations[{{ $index }}][code]"
                                               id="update_code_{{ $variation->id }}"
                                               value="{{ old('update_variations.' . $index . '.code', $variation->code) }}"
                                               required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                        <input type="hidden" name="update_variations[{{ $index }}][id]" value="{{ $variation->id }}">
                                    </div>

                                    <div>
                                        <label for="update_size_{{ $variation->id }}" class="block text-sm font-medium text-gray-700 mb-1">Tamanho <span class="text-red-500">*</span></label>
                                        <select name="update_variations[{{ $index }}][size]"
                                                id="update_size_{{ $variation->id }}"
                                                required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                            <option value="">Selecione</option>
                                            @foreach(['PP', 'P', 'M', 'G', 'GG', 'XG', 'XXG', 'XXXG', 'Único'] as $size)
                                                @php
                                                    $currentSize = old('update_variations.' . $index . '.size', $variation->size);
                                                    $normalizedCurrentSize = strtoupper($currentSize);
                                                    if ($normalizedCurrentSize === 'G1') $normalizedCurrentSize = 'G';
                                                @endphp
                                                <option value="{{ $size }}"
                                                    {{ $normalizedCurrentSize == $size ? 'selected' : '' }}>
                                                    {{ $size }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="update_color_{{ $variation->id }}" class="block text-sm font-medium text-gray-700 mb-1">Cor</label>
                                        <select name="update_variations[{{ $index }}][color]"
                                                id="update_color_{{ $variation->id }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                            <option value="">Sem cor específica</option>
                                            @foreach($colors as $color)
                                                @php
                                                    $currentColor = old('update_variations.' . $index . '.color', $variation->color);
                                                    $colorName = $color['name'];
                                                    $isSelected = false;

                                                    if ($currentColor && str_starts_with($currentColor, '#')) {
                                                        switch($currentColor) {
                                                            case '#000000':
                                                                $isSelected = ($colorName === 'Preto');
                                                                break;
                                                            case '#FFFFFF':
                                                                $isSelected = ($colorName === 'Branco');
                                                                break;
                                                            case '#808080':
                                                                $isSelected = ($colorName === 'Cinza');
                                                                break;
                                                            case '#FF0000':
                                                                $isSelected = ($colorName === 'Vermelho');
                                                                break;
                                                            case '#FFC0CB':
                                                                $isSelected = ($colorName === 'Rosa');
                                                                break;
                                                            case '#0000FF':
                                                            case '#000080':
                                                                $isSelected = ($colorName === 'Azul');
                                                                break;
                                                            case '#008000':
                                                                $isSelected = ($colorName === 'Verde');
                                                                break;
                                                            case '#FFFF00':
                                                                $isSelected = ($colorName === 'Amarelo');
                                                                break;
                                                            case '#800080':
                                                                $isSelected = ($colorName === 'Roxo');
                                                                break;
                                                            case '#FFA500':
                                                                $isSelected = ($colorName === 'Laranja');
                                                                break;
                                                        }
                                                    } else {
                                                        $isSelected = ($currentColor === $colorName);
                                                    }
                                                @endphp
                                                <option value="{{ $color['name'] }}"
                                                    {{ $isSelected ? 'selected' : '' }}>
                                                    {{ $color['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="update_stock_{{ $variation->id }}" class="block text-sm font-medium text-gray-700 mb-1">Estoque <span class="text-red-500">*</span></label>
                                        <input type="number"
                                               name="update_variations[{{ $index }}][stock]"
                                               id="update_stock_{{ $variation->id }}"
                                               value="{{ old('update_variations.' . $index . '.stock', $variation->stock) }}"
                                               required
                                               min="0"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="border-b border-gray-200 pb-6 mb-6">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-yellow-800">Este produto ainda não possui SKUs/variações. Adicione pelo menos um SKU abaixo.</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">Adicionar Novos SKUs</h2>
                        <button type="button" class="add-sku-btn" onclick="addSkuItem()">+ Adicionar SKU</button>
                    </div>

                    <div id="skus-container">
                        <!-- Novos SKUs serão adicionados aqui via JavaScript -->
                    </div>

                    @error('skus')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-8">
                    <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-colors">
                        Atualizar Produto
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let skuCounter = 0;

        window.addEventListener('load', function() {
            const form = document.getElementById('productEditForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    return handleFormSubmit(e);
                });
            }
        });

        function handleFormSubmit(e) {
            const form = document.getElementById('productEditForm');

            if (!form) {
                alert('Formulário não encontrado!');
                e.preventDefault();
                return false;
            }

            const name = form.querySelector('input[name="name"]');

            if (!name || !name.value.trim()) {
                alert('Nome é obrigatório!');
                e.preventDefault();
                return false;
            }

            form.action = '{{ route("admin.products.update.post", $product->id) }}';

            e.preventDefault();

            setTimeout(() => {
                form.submit();
            }, 100);

            return false;
        }

        function addSkuItem() {
            const container = document.getElementById('skus-container');
            if (!container) {
                return;
            }

            const skuHtml = `
                <div class="sku-item" id="sku-${skuCounter}" style="border: 1px solid #ccc; padding: 15px; margin: 10px 0; border-radius: 5px;">
                    <div class="sku-header" style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                        <h4>Novo SKU #${skuCounter + 1}</h4>
                        <button type="button" onclick="removeSku(${skuCounter})" style="background: red; color: white; border: none; padding: 5px 10px; border-radius: 3px;">
                            Remover
                        </button>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 15px;">
                        <div>
                            <label>Código SKU:</label>
                            <input type="text" name="skus[${skuCounter}][code]" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px;">
                        </div>

                        <div>
                            <label>Tamanho:</label>
                            <select name="skus[${skuCounter}][size]" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px;">
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
                            <label>Cor:</label>
                            <select name="skus[${skuCounter}][color]" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px;">
                                <option value="">Sem cor</option>
                                <option value="Preto">Preto</option>
                                <option value="Branco">Branco</option>
                                <option value="Cinza">Cinza</option>
                                <option value="Vermelho">Vermelho</option>
                                <option value="Rosa">Rosa</option>
                                <option value="Azul">Azul</option>
                                <option value="Verde">Verde</option>
                                <option value="Amarelo">Amarelo</option>
                            </select>
                        </div>

                        <div>
                            <label>Estoque:</label>
                            <input type="number" name="skus[${skuCounter}][stock]" value="0" min="0" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px;">
                        </div>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', skuHtml);
            skuCounter++;
        }

        function removeSku(id) {
            const element = document.getElementById('sku-' + id);
            if (element) {
                element.remove();
            }
        }

        function deleteImage(productId, imageId) {
            if (!confirm('Excluir imagem?')) return;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/products/${productId}/images/${imageId}`;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';

            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }

        function deleteVariation(productId, variationId) {
            if (!confirm('Tem certeza que deseja excluir este SKU?')) return;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/products/${productId}/variations/${variationId}`;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';

            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
@endsection
