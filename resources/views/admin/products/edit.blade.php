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
    <div class="container mx-auto px-4">
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
                        <input type="text" name="subcategory" id="subcategory" value="{{ old('subcategory', $product->subcategory) }}" required
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
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-4" id="images-container">
                            @foreach($product->images as $image)
                                <div class="relative group border rounded-lg p-1 image-item" data-id="{{ $image->id }}">
                                    <div class="aspect-w-1 aspect-h-1 overflow-hidden rounded-md bg-gray-200">
                                        <img src="{{ asset($image->image_path) }}" alt="{{ $product->name }}" class="object-cover">

                                        @if($image->is_main)
                                            <div class="absolute top-2 left-2 bg-pink-500 text-white px-2 py-1 rounded-full text-xs">
                                                Principal
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mt-2 flex justify-between">
                                        @if(!$image->is_main)
                                            <form action="{{ route('admin.products.images.main', [$product->id, $image->id]) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="text-xs text-blue-600 hover:text-blue-800">
                                                    Definir como principal
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-500">Imagem principal</span>
                                        @endif

                                        <form action="{{ route('admin.products.images.delete', [$product->id, $image->id]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 hover:text-red-800" onclick="return confirm('Tem certeza que deseja excluir esta imagem?')">
                                                Excluir
                                            </button>
                                        </form>
                                    </div>

                                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 hidden group-hover:block">
                                        <div class="bg-white bg-opacity-70 p-2 rounded cursor-move">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 mb-4">Este produto ainda não possui imagens.</p>
                    @endif

                    <!-- Upload de novas imagens -->
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

                <!-- Tamanhos -->
                <div class="mt-6" x-data="{ selectedSizes: {{ json_encode($sizes->toArray()) }} }">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tamanhos Disponíveis</label>
                    <div class="sizes-container">
                        @foreach(['PP', 'P', 'M', 'G', 'GG', 'XG', 'XXG', 'XXXG', 'Único'] as $size)
                            <label class="size-option">
                                <input type="checkbox" name="sizes[]" value="{{ $size }}"
                                    {{ in_array($size, old('sizes', $sizes->toArray())) ? 'checked' : '' }}
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
                                    <input type="number" :name="'stock_' + size" :id="'stock_' + size"
                                        :value="getStockForSize(size)"
                                        min="0" step="1"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Cores -->
                <div class="mt-6" x-data="{ selectedColors: {{ json_encode($colors->toArray()) }}, showColorInput: false, newColor: '' }">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cores Disponíveis</label>
                    <div class="colors-container">
                        @foreach($colors as $color)
                            <label class="color-option">
                                <input type="checkbox" name="colors[]" value="{{ $color['hex'] }}"
                                    {{ in_array($color['hex'], old('colors', $selectedColors->toArray())) ? 'checked' : '' }}
                                    @click="selectedColors.includes('{{ $color['hex'] }}') ? selectedColors = selectedColors.filter(c => c !== '{{ $color['hex'] }}') : selectedColors.push('{{ $color['hex'] }}')">
                                {{ $color['name'] }}
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
                        Atualizar Produto
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    function getStockForSize(size) {
        // Buscar estoque para este tamanho nas variações do produto
        @php
            $stockBySize = [];
            foreach($product->variations as $variation) {
                if (!isset($stockBySize[$variation->size])) {
                    $stockBySize[$variation->size] = $variation->stock;
                }
            }
            echo "const stockBySize = " . json_encode($stockBySize) . ";";
        @endphp

        return stockBySize[size] || 0;
    }
</script>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar drag & drop para reordenar imagens
        const imagesContainer = document.getElementById('images-container');
        if (imagesContainer) {
            new Sortable(imagesContainer, {
                animation: 150,
                ghostClass: 'bg-gray-100',
                onEnd: function() {
                    // Salvar a nova ordem
                    saveOrder();
                }
            });
        }

        // Função para salvar a ordem das imagens
        function saveOrder() {
            const imageItems = document.querySelectorAll('.image-item');
            const imageIds = Array.from(imageItems).map(item => item.dataset.id);

            fetch('{{ route("admin.products.images.reorder", $product->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    imageIds: imageIds
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Ordem salva com sucesso
                }
            })
            .catch(error => {
                console.error('Erro ao salvar a ordem das imagens:', error);
            });
        }
    });

    function getStockForSize(size) {
        @php
            $stockBySize = [];
            foreach($product->variations as $variation) {
                if (!isset($stockBySize[$variation->size])) {
                    $stockBySize[$variation->size] = $variation->stock;
                }
            }
            echo "const stockBySize = " . json_encode($stockBySize) . ";";
        @endphp

        return stockBySize[size] || 0;
    }
</script>
@endsection
