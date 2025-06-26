@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="mb-6">
            <a href="{{ route('admin.sellers.index') }}" class="text-blue-500 hover:text-blue-700">
                &larr; Voltar para lista de vendedores
            </a>
            <h1 class="text-2xl font-bold text-gray-800 mt-2">Editar Vendedor: {{ $seller->name }}</h1>
        </div>

        <!-- Mensagens de Sucesso/Erro -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Formulário de Dados Básicos - ACIMA -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Dados Básicos</h2>

            <form method="POST" action="{{ route('admin.sellers.update', $seller->id) }}">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Nome <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               id="name"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                               value="{{ old('name', $seller->name) }}"
                               required>
                        @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email"
                               name="email"
                               id="email"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                               value="{{ old('email', $seller->email) }}"
                               required>
                        @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="commission" class="block text-sm font-medium text-gray-700 mb-1">
                            Comissão (%)
                        </label>
                        <input type="number"
                               name="commission"
                               id="commission"
                               step="0.01"
                               min="0"
                               max="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('commission') border-red-500 @enderror"
                               placeholder="0.00"
                               value="{{ old('commission', $seller->commission) }}">
                        @error('commission')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 text-xs mt-1">Percentual de comissão sobre vendas (0-100%)</p>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            Nova Senha (opcional)
                        </label>
                        <div class="relative">
                            <input type="password"
                                   name="password"
                                   id="password"
                                   class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror"
                                   placeholder="Deixe em branco para manter a senha atual">
                            <button type="button"
                                    id="togglePassword"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg id="eyeOpen" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="eyeClosed" class="h-5 w-5 text-gray-400 hover:text-gray-600 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 text-xs mt-1">Mínimo 8 caracteres (deixe em branco para não alterar)</p>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-md">
                        Atualizar Dados Básicos
                    </button>
                </div>
            </form>
        </div>

        <!-- Gestão de Estoque - ABAIXO -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Gestão de Estoque</h2>

            <!-- Adicionar Produto por SKU -->
            <div class="border-b border-gray-200 pb-4 mb-4">
                <h3 class="text-md font-medium text-gray-700 mb-3">Adicionar Produto</h3>

                <div class="space-y-3">
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Buscar SKU</label>
                        <input type="text"
                               id="sku-search"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Digite o código do SKU">
                        <div id="sku-suggestions" class="hidden absolute z-10 bg-white border border-gray-300 rounded-md shadow-lg max-h-40 overflow-y-auto w-full mt-1"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Produto</label>
                            <input type="text"
                                   id="selected-product"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100"
                                   readonly
                                   placeholder="Selecione um SKU">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estoque Disponível</label>
                            <input type="text"
                                   id="available-stock"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100"
                                   readonly>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade</label>
                            <input type="number"
                                   id="stock-quantity"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   min="1"
                                   placeholder="0">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="button"
                                id="add-stock-btn"
                                class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded-md disabled:bg-gray-300"
                                disabled>
                            Adicionar ao Estoque
                        </button>
                    </div>

                    <div id="stock-message" class="hidden text-sm"></div>
                </div>
            </div>

            <!-- Lista de Estoque Atual -->
            <div>
                <h3 class="text-md font-medium text-gray-700 mb-3">Estoque Atual</h3>
                <div id="current-stock-list" class="space-y-2 max-h-64 overflow-y-auto">
                    @forelse($sellerStocks as $stock)
                        @php
                            // Buscar a variação para obter cor e tamanho
                            $variation = \App\Models\ProductVariation::where('product_id', $stock->product_id)->first();

                            // Mapeamento de cores hex para nomes
                            $colorMap = [
                                '#000000' => 'Preto',
                                '#FFFFFF' => 'Branco',
                                '#808080' => 'Cinza',
                                '#FF0000' => 'Vermelho',
                                '#FFC0CB' => 'Rosa',
                                '#0000FF' => 'Azul',
                                '#000080' => 'Azul',
                                '#008000' => 'Verde',
                                '#FFFF00' => 'Amarelo',
                                '#800080' => 'Roxo',
                                '#FFA500' => 'Laranja',
                                '#A52A2A' => 'Marrom',
                                '#F5CBA7' => 'Bege'
                            ];

                            $colorDisplay = '';
                            if ($variation && $variation->color) {
                                $colorDisplay = $colorMap[$variation->color] ?? $variation->color;
                            }

                            $sizeDisplay = $variation ? $variation->size : '';
                        @endphp
                        <div class="stock-item flex items-center justify-between p-3 bg-gray-50 rounded-md" data-product-id="{{ $stock->product_id }}">
                            <div>
                                <p class="font-medium text-gray-900">{{ $stock->product->name ?? 'Produto não encontrado' }}</p>
                                <div class="text-sm text-gray-600">
                                    <span>{{ $stock->quantity }} unidades</span>
                                    @if($sizeDisplay || $colorDisplay)
                                        <span class="ml-2">
                                            {{ $sizeDisplay }}{{ $colorDisplay ? ' - ' . $colorDisplay : '' }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <button type="button"
                                    class="remove-stock-btn text-red-500 hover:text-red-700 text-sm font-medium"
                                    data-product-id="{{ $stock->product_id }}">
                                Remover
                            </button>
                        </div>
                    @empty
                        <div id="no-stock-message" class="text-center py-8 text-gray-500">
                            <p>Nenhum produto no estoque</p>
                            <p class="text-sm">Use o campo acima para adicionar produtos</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var selectedProductId = null;
            var selectedVariationId = null;
            var allVariations = [];

            // Toggle senha
            var passwordToggle = document.getElementById('togglePassword');
            var passwordInput = document.getElementById('password');
            var eyeOpen = document.getElementById('eyeOpen');
            var eyeClosed = document.getElementById('eyeClosed');

            if (passwordToggle) {
                passwordToggle.addEventListener('click', function() {
                    var type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    if (type === 'text') {
                        eyeOpen.classList.add('hidden');
                        eyeClosed.classList.remove('hidden');
                    } else {
                        eyeOpen.classList.remove('hidden');
                        eyeClosed.classList.add('hidden');
                    }
                });
            }

            // Carregar variações do backend
            try {
                @php
                    if (isset($variations) && method_exists($variations, 'count') && $variations->count() > 0) {
                        $variationsArray = [];
                        foreach ($variations as $variation) {
                            $variationsArray[] = [
                                'id' => $variation->id ?? 0,
                                'code' => $variation->code ?? '',
                                'size' => $variation->size ?? '',
                                'color' => $variation->color ?? '',
                                'stock' => $variation->stock ?? 0,
                                'product_id' => $variation->product_id ?? 0,
                                'product_name' => ($variation->product && $variation->product->name) ? $variation->product->name : 'Produto não encontrado',
                            ];
                        }
                        echo 'allVariations = ' . json_encode($variationsArray) . ';';
                    } else {
                        echo 'allVariations = [];';
                    }
                @endphp
            } catch (e) {
                allVariations = [];
            }

            var colorMap = {
                '#000000': 'Preto', '#FFFFFF': 'Branco', '#808080': 'Cinza',
                '#FF0000': 'Vermelho', '#FFC0CB': 'Rosa', '#0000FF': 'Azul',
                '#000080': 'Azul', '#008000': 'Verde', '#FFFF00': 'Amarelo',
                '#800080': 'Roxo', '#FFA500': 'Laranja', '#A52A2A': 'Marrom',
                '#F5CBA7': 'Bege'
            };

            function getColorName(colorValue) {
                if (!colorValue || !colorValue.startsWith('#')) return '';
                return colorMap[colorValue.toUpperCase()] || '';
            }

            // Busca de SKU
            var skuSearch = document.getElementById('sku-search');
            var suggestions = document.getElementById('sku-suggestions');

            if (skuSearch) {
                skuSearch.addEventListener('input', function() {
                    var term = this.value.toLowerCase();

                    if (term.length < 2) {
                        suggestions.classList.add('hidden');
                        return;
                    }

                    var matches = allVariations.filter(function(v) {
                        return (v.code && v.code.toLowerCase().includes(term)) ||
                            (v.product_name && v.product_name.toLowerCase().includes(term));
                    });

                    if (matches.length > 0) {
                        var html = '';
                        matches.forEach(function(v) {
                            var colorName = getColorName(v.color);
                            html += '<div class="suggestion px-3 py-2 hover:bg-gray-100 cursor-pointer border-b" data-variation="' + JSON.stringify(v).replace(/"/g, '&quot;') + '">';
                            html += '<div class="font-medium">' + v.code + '</div>';
                            html += '<div class="text-sm text-gray-600">' + v.product_name + ' - ' + v.size + (colorName ? ' (' + colorName + ')' : '') + '</div>';
                            html += '<div class="text-xs text-green-600">Estoque: ' + v.stock + '</div>';
                            html += '</div>';
                        });
                        suggestions.innerHTML = html;
                        suggestions.classList.remove('hidden');
                    } else {
                        suggestions.innerHTML = '<div class="px-3 py-2 text-gray-500">Nenhum resultado</div>';
                        suggestions.classList.remove('hidden');
                    }
                });
            }

            // Seleção de SKU
            if (suggestions) {
                suggestions.addEventListener('click', function(e) {
                    var item = e.target.closest('.suggestion');
                    if (!item) return;

                    var variationData = item.getAttribute('data-variation');
                    var variation = JSON.parse(variationData.replace(/&quot;/g, '"'));

                    selectedProductId = variation.product_id;
                    selectedVariationId = variation.id;
                    document.getElementById('sku-search').value = variation.code;
                    document.getElementById('selected-product').value = variation.product_name + ' - ' + variation.size;
                    document.getElementById('available-stock').value = variation.stock;
                    document.getElementById('add-stock-btn').disabled = false;

                    suggestions.classList.add('hidden');
                });
            }

            // Adicionar ao estoque
            var addStockBtn = document.getElementById('add-stock-btn');
            if (addStockBtn) {
                addStockBtn.addEventListener('click', function() {
                    var quantity = parseInt(document.getElementById('stock-quantity').value) || 0;
                    var availableStock = parseInt(document.getElementById('available-stock').value) || 0;

                    if (!selectedProductId || !selectedVariationId || quantity <= 0) {
                        showMessage('Selecione um SKU e digite uma quantidade válida.', 'error');
                        return;
                    }

                    if (quantity > availableStock) {
                        showMessage('Quantidade maior que o estoque disponível (' + availableStock + ').', 'error');
                        return;
                    }

                    // AJAX para adicionar estoque
                    var url = '{{ url("/admin/sellers/" . $seller->id . "/add-stock") }}';

                    var csrfToken = document.querySelector('meta[name="csrf-token"]');
                    var token = csrfToken ? csrfToken.getAttribute('content') : '{{ csrf_token() }}';

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({
                            product_id: selectedProductId,
                            product_variation_id: selectedVariationId,
                            quantity: quantity
                        })
                    })
                        .then(response => {
                            // Verificar se a resposta é JSON
                            const contentType = response.headers.get('content-type');
                            if (!contentType || !contentType.includes('application/json')) {
                                return response.text().then(text => {
                                    throw new Error('Resposta não é JSON: ' + text.substring(0, 100));
                                });
                            }

                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                showMessage(data.message, 'success');
                                addStockToList(data.stock);
                                clearForm();
                            } else {
                                showMessage(data.message || 'Erro ao adicionar produto.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Erro completo:', error);
                            showMessage('Erro: ' + error.message, 'error');
                        });
                });
            }

            // Remover do estoque
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-stock-btn')) {
                    var productId = e.target.getAttribute('data-product-id');
                    var stockId = e.target.getAttribute('data-stock-id');

                    if (confirm('Tem certeza que deseja remover este produto do estoque?')) {
                        var removeUrl = '{{ url("/admin/sellers/" . $seller->id . "/remove-stock") }}';

                        fetch(removeUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify({
                                product_id: productId,
                                stock_id: stockId
                            })
                        })
                            .then(response => {
                                const contentType = response.headers.get('content-type');
                                if (!contentType || !contentType.includes('application/json')) {
                                    return response.text().then(text => {
                                        throw new Error('Resposta não é JSON: ' + text.substring(0, 100));
                                    });
                                }

                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    showMessage(data.message, 'success');
                                    removeStockFromList(stockId);
                                } else {
                                    showMessage(data.message || 'Erro ao remover produto.', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Erro completo na remoção:', error);
                                showMessage('Erro: ' + error.message, 'error');
                            });
                    }
                }
            });

            function showMessage(message, type) {
                var messageDiv = document.getElementById('stock-message');
                messageDiv.textContent = message;
                messageDiv.className = 'text-sm ' + (type === 'success' ? 'text-green-600' : 'text-red-600');
                messageDiv.classList.remove('hidden');

                setTimeout(function() {
                    messageDiv.classList.add('hidden');
                }, 3000);
            }

            function addStockToList(stock) {
                var stockList = document.getElementById('current-stock-list');
                var noStockMessage = document.getElementById('no-stock-message');

                if (noStockMessage) {
                    noStockMessage.remove();
                }

                // Verificar se já existe um item para este produto específico
                var existingItem = stockList.querySelector('[data-product-id="' + stock.product_id + '"]');
                if (existingItem) {
                    // Atualizar quantidade do item existente
                    var quantityElement = existingItem.querySelector('.text-sm span');
                    if (quantityElement) {
                        quantityElement.textContent = stock.quantity + ' unidades';
                    }
                } else {
                    // Adicionar novo item
                    var html = '<div class="stock-item flex items-center justify-between p-3 bg-gray-50 rounded-md" data-product-id="' + stock.product_id + '" data-stock-id="' + (stock.stock_id || stock.id || '') + '">';
                    html += '<div>';
                    html += '<p class="font-medium text-gray-900">' + stock.product_name + '</p>';
                    html += '<div class="text-sm text-gray-600">';
                    html += '<span>' + stock.quantity + ' unidades</span>';
                    if (stock.variations && stock.variations.length > 0) {
                        html += '<div class="text-xs text-gray-500 mt-1">Variações disponíveis: ';
                        stock.variations.slice(0, 3).forEach(function(variation, index) {
                            var colorName = variation.color ? getColorName(variation.color) : '';
                            html += '<span class="inline-block bg-white px-2 py-1 rounded mr-1 mb-1">';
                            html += variation.code + ' (' + variation.size + (colorName ? ' - ' + colorName : '') + ')';
                            html += '</span>';
                        });
                        if (stock.variations.length > 3) {
                            html += '<span class="text-gray-400">+' + (stock.variations.length - 3) + ' mais...</span>';
                        }
                        html += '</div>';
                    }
                    html += '</div>';
                    html += '</div>';
                    html += '<button type="button" class="remove-stock-btn text-red-500 hover:text-red-700 text-sm font-medium" data-product-id="' + stock.product_id + '" data-stock-id="' + (stock.stock_id || stock.id || '') + '">Remover</button>';
                    html += '</div>';

                    stockList.insertAdjacentHTML('beforeend', html);
                }
            }

            function removeStockFromList(stockId) {
                var item = document.querySelector('[data-stock-id="' + stockId + '"]');
                if (item) {
                    item.remove();
                }

                var stockList = document.getElementById('current-stock-list');
                if (stockList.children.length === 0) {
                    var html = '<div id="no-stock-message" class="text-center py-8 text-gray-500">';
                    html += '<p>Nenhum produto no estoque</p>';
                    html += '<p class="text-sm">Use o campo acima para adicionar produtos</p>';
                    html += '</div>';
                    stockList.innerHTML = html;
                }
            }

            function clearForm() {
                document.getElementById('sku-search').value = '';
                document.getElementById('selected-product').value = '';
                document.getElementById('stock-quantity').value = '';
                document.getElementById('available-stock').value = '';
                document.getElementById('add-stock-btn').disabled = true;
                selectedProductId = null;
                selectedVariationId = null;
            }

            // Fechar sugestões ao clicar fora
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#sku-search') && !e.target.closest('#sku-suggestions')) {
                    suggestions.classList.add('hidden');
                }
            });
        });
    </script>
@endsection
