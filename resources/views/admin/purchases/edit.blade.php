@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="mb-6">
            <a href="{{ route('admin.purchases.index') }}" class="text-blue-500 hover:text-blue-700">
                &larr; Voltar para lista de compras
            </a>
            <h1 class="text-2xl font-bold text-gray-800 mt-2">Editar Compra #{{ $purchase->id }}</h1>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form method="POST" action="{{ route('admin.purchases.update', $purchase->id) }}" id="purchase-form">
                @csrf
                @method('PUT')

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

                <!-- Dados da Compra -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Dados da Compra</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="supplier_name" class="block text-sm font-medium text-gray-700 mb-1">Fornecedor <span class="text-red-500">*</span></label>
                            <input type="text" name="supplier_name" id="supplier_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nome do fornecedor" value="{{ old('supplier_name', $purchase->supplier_name) }}" required>
                            @error('supplier_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="purchase_date" class="block text-sm font-medium text-gray-700 mb-1">Data da Compra <span class="text-red-500">*</span></label>
                            <input type="date" name="purchase_date" id="purchase_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('purchase_date', $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('Y-m-d') : '') }}" required>
                            @error('purchase_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="installments" class="block text-sm font-medium text-gray-700 mb-1">Parcelas <span class="text-red-500">*</span></label>
                            <input type="number" name="installments" id="installments" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Qtd de parcelas" value="{{ old('installments', $purchase->installments) }}" min="1" required>
                            @error('installments')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="installment_value" class="block text-sm font-medium text-gray-700 mb-1">Valor da Parcela</label>
                            <input type="text" id="installment_value" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none" readonly>
                        </div>

                        <div class="md:col-span-2">
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Método de Pagamento <span class="text-red-500">*</span></label>
                            <select name="payment_method" id="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Selecione o método</option>
                                <option value="PIX" {{ old('payment_method', $purchase->payment_method) == 'PIX' ? 'selected' : '' }}>PIX</option>
                                <option value="Cartão de Crédito" {{ old('payment_method', $purchase->payment_method) == 'Cartão de Crédito' ? 'selected' : '' }}>Cartão de Crédito</option>
                                <option value="Cartão de Débito" {{ old('payment_method', $purchase->payment_method) == 'Cartão de Débito' ? 'selected' : '' }}>Cartão de Débito</option>
                                <option value="Dinheiro" {{ old('payment_method', $purchase->payment_method) == 'Dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                                <option value="Boleto" {{ old('payment_method', $purchase->payment_method) == 'Boleto' ? 'selected' : '' }}>Boleto</option>
                            </select>
                            @error('payment_method')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- SKUs/Produtos Existentes -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">SKUs na Compra ({{ $purchase->items->count() }})</h2>

                    <div id="existing-items-container">
                        @foreach ($purchase->items as $index => $item)
                            <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg mb-4 existing-item-row" id="existing-item-{{ $item->id }}">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="font-medium text-gray-700">SKU: {{ $item->variation->code ?? 'Código não encontrado' }}</h4>
                                    <button type="button" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm" onclick="removeExistingItem({{ $item->id }})">Remover</button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none" value="{{ $item->variation->code ?? 'N/A' }}" readonly>
                                        <input type="hidden" name="existing_items[{{ $item->id }}][id]" value="{{ $item->id }}">
                                        <input type="hidden" name="existing_items[{{ $item->id }}][product_id]" value="{{ $item->variation->product_id ?? '' }}">
                                        <input type="hidden" name="existing_items[{{ $item->id }}][product_variation_id]" value="{{ $item->product_variation_id }}">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Produto</label>
                                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none" value="{{ $item->variation->product->name ?? 'Produto não encontrado' }}" readonly>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Variação</label>
                                        @php
                                            $colorDisplay = '';
                                            if ($item->variation && $item->variation->color) {
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
                                                $colorDisplay = $colorMap[$item->variation->color] ?? $item->variation->color;
                                            }
                                        @endphp
                                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none" value="{{ $item->variation ? $item->variation->size . ($colorDisplay ? ' - ' . $colorDisplay : '') : 'N/A' }}" readonly>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade <span class="text-red-500">*</span></label>
                                        <input type="number" name="existing_items[{{ $item->id }}][quantity]" class="existing-quantity w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('existing_items.' . $item->id . '.quantity', $item->quantity) }}" min="1" required>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Preço Unitário (R$) <span class="text-red-500">*</span></label>
                                        <input type="number" step="0.01" name="existing_items[{{ $item->id }}][unit_price]" class="existing-unit-price w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('existing_items.' . $item->id . '.unit_price', $item->unit_price) }}" min="0" required>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal</label>
                                        <input type="text" class="existing-subtotal w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none" readonly>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Estoque Atual</label>
                                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none" value="{{ $item->variation->stock ?? 'N/A' }}" readonly>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Novos SKUs -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">Adicionar Novos SKUs</h2>
                        <button type="button" id="add-item" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">Adicionar SKU</button>
                    </div>

                    <div id="items-container">
                        <!-- Novos itens serão adicionados aqui -->
                    </div>
                </div>

                <!-- Total -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total da Compra</label>
                            <input type="text" id="total_display" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none text-lg font-semibold" readonly>
                            <input type="hidden" name="total" id="total_value">
                            <input type="hidden" name="installment_value" id="installment_value_for_validation">
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-md focus:outline-none focus:shadow-outline transition-colors">
                        Atualizar Compra
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Debug: Mostrar dados do item -->
    <div style="display: none;">
        <h3>Debug - Dados dos Itens:</h3>
        @foreach ($purchase->items as $index => $item)
            <p>Item {{ $index }}:</p>
            <p>- ID: {{ $item->id }}</p>
            <p>- Variation ID: {{ $item->product_variation_id }}</p>
            <p>- Code: {{ $item->variation->code ?? 'NULL' }}</p>
            <p>- Product Name: {{ $item->variation->product->name ?? 'NULL' }}</p>
            <p>- Size: {{ $item->variation->size ?? 'NULL' }}</p>
            <p>- Color: {{ $item->variation->color ?? 'NULL' }}</p>
            <p>- Stock: {{ $item->variation->stock ?? 'NULL' }}</p>
            <hr>
        @endforeach
    </div>

    <script>
        let itemCount = 0;
        let itemsToDelete = [];

        // Variações disponíveis - obtidas do backend
        let allVariations = [];

        // Mapeamento de cores hex para nomes
        const colorHexToName = {
            '#000000': 'Preto',
            '#FFFFFF': 'Branco',
            '#808080': 'Cinza',
            '#FF0000': 'Vermelho',
            '#FFC0CB': 'Rosa',
            '#0000FF': 'Azul',
            '#000080': 'Azul',
            '#008000': 'Verde',
            '#FFFF00': 'Amarelo',
            '#800080': 'Roxo',
            '#FFA500': 'Laranja',
            '#A52A2A': 'Marrom',
            '#F5CBA7': 'Bege'
        };

        // Função para converter hex para nome da cor
        function getColorName(colorValue) {
            if (!colorValue) return '';

            // Se já é um nome de cor, retorna como está
            if (!colorValue.startsWith('#')) {
                return colorValue;
            }

            // Se é hex, converte para nome
            return colorHexToName[colorValue.toUpperCase()] || colorValue;
        }

        @php
            $variationsData = $products->pluck('variations')->flatten(1)->map(function($variation) {
                return [
                    'id' => $variation->id,
                    'code' => $variation->code,
                    'size' => $variation->size,
                    'color' => $variation->color,
                    'stock' => $variation->stock,
                    'product_id' => $variation->product_id,
                    'product_name' => $variation->product->name ?? 'Produto não encontrado',
                    'product_purchase_price' => $variation->product->purchase_price ?? 0
                ];
            });
        @endphp

            allVariations = @json($variationsData);

        function setupSkuSearch(container) {
            const skuInput = container.querySelector('.sku-search');
            const suggestionsDiv = container.querySelector('.sku-suggestions');
            const productIdInput = container.querySelector('.product-id');
            const variationIdInput = container.querySelector('.variation-id');
            const productNameInput = container.querySelector('.product-name');
            const variationInfoInput = container.querySelector('.variation-info');
            const unitPriceInput = container.querySelector('.unit-price');
            const currentStockInput = container.querySelector('.current-stock');

            skuInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();

                if (searchTerm.length < 1) {
                    suggestionsDiv.classList.add('hidden');
                    return;
                }

                const matches = allVariations.filter(variation =>
                    variation.code.toLowerCase().includes(searchTerm) ||
                    variation.product_name.toLowerCase().includes(searchTerm)
                );

                if (matches.length > 0) {
                    let suggestionsHtml = '';
                    matches.forEach(variation => {
                        const variationJson = JSON.stringify(variation).replace(/"/g, '&quot;');
                        const colorName = getColorName(variation.color);
                        const displayColor = colorName || variation.color;

                        suggestionsHtml += `
                            <div class="suggestion-item px-3 py-2 hover:bg-gray-100 cursor-pointer border-b" data-variation="${variationJson}">
                                <div class="font-medium">${variation.code}</div>
                                <div class="text-sm text-gray-600">${variation.product_name} - ${variation.size}${displayColor ? ' (' + displayColor + ')' : ''}</div>
                                <div class="text-xs text-gray-500">Estoque: ${variation.stock} | Preço Sugerido: R$ ${parseFloat(variation.product_purchase_price).toFixed(2).replace('.', ',')}</div>
                            </div>
                        `;
                    });
                    suggestionsDiv.innerHTML = suggestionsHtml;
                    suggestionsDiv.classList.remove('hidden');
                } else {
                    suggestionsDiv.innerHTML = '<div class="px-3 py-2 text-gray-500">Nenhum SKU encontrado</div>';
                    suggestionsDiv.classList.remove('hidden');
                }
            });

            suggestionsDiv.addEventListener('click', function(e) {
                const suggestionItem = e.target.closest('.suggestion-item');
                if (suggestionItem) {
                    const variationJson = suggestionItem.getAttribute('data-variation');
                    const variation = JSON.parse(variationJson.replace(/&quot;/g, '"'));

                    skuInput.value = variation.code;
                    productIdInput.value = variation.product_id;
                    variationIdInput.value = variation.id;
                    productNameInput.value = variation.product_name;

                    // Converter cor hex para nome antes de exibir
                    const colorName = getColorName(variation.color);
                    variationInfoInput.value = variation.size + (colorName ? ' - ' + colorName : '');

                    unitPriceInput.value = parseFloat(variation.product_purchase_price).toFixed(2);
                    currentStockInput.value = variation.stock;

                    suggestionsDiv.classList.add('hidden');
                    calculateSubtotal(container);
                    calculateTotal();
                }
            });

            // Fechar sugestões ao clicar fora
            document.addEventListener('click', function(e) {
                if (!container.contains(e.target)) {
                    suggestionsDiv.classList.add('hidden');
                }
            });
        }

        function calculateSubtotal(container) {
            const quantity = parseFloat(container.querySelector('.quantity, .existing-quantity').value) || 0;
            const unitPrice = parseFloat(container.querySelector('.unit-price, .existing-unit-price').value) || 0;
            const subtotal = quantity * unitPrice;

            const subtotalInput = container.querySelector('.subtotal, .existing-subtotal');
            if (subtotalInput) {
                subtotalInput.value = 'R$ ' + subtotal.toFixed(2).replace('.', ',');
            }
        }

        function calculateTotal() {
            let total = 0;

            // Somar itens existentes
            document.querySelectorAll('.existing-item-row:not(.to-delete)').forEach(row => {
                const quantity = parseFloat(row.querySelector('.existing-quantity').value) || 0;
                const unitPrice = parseFloat(row.querySelector('.existing-unit-price').value) || 0;
                total += quantity * unitPrice;
            });

            // Somar novos itens
            document.querySelectorAll('.new-item-row').forEach(row => {
                const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
                const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
                total += quantity * unitPrice;
            });

            document.getElementById('total_display').value = 'R$ ' + total.toFixed(2).replace('.', ',');
            document.getElementById('total_value').value = total.toFixed(2);

            calculateInstallmentValue(total);
        }

        function calculateInstallmentValue(total) {
            const installments = parseInt(document.getElementById('installments').value) || 1;
            const value = (total / installments).toFixed(2);
            document.getElementById('installment_value').value = 'R$ ' + value.replace('.', ',');
            document.getElementById('installment_value_for_validation').value = value;
        }

        function addNewItem() {
            const container = document.getElementById('items-container');

            const newItemHtml = `
                <div class="bg-gray-50 p-4 rounded-lg mb-4 new-item-row">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-medium text-gray-700">Novo SKU #${itemCount + 1}</h4>
                        <button type="button" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm remove-item">Remover</button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar SKU <span class="text-red-500">*</span></label>
                            <input type="text" class="sku-search w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Digite o código do SKU" required>
                            <input type="hidden" name="items[${itemCount}][product_id]" class="product-id" required>
                            <input type="hidden" name="items[${itemCount}][product_variation_id]" class="variation-id" required>
                            <div class="sku-suggestions hidden absolute z-10 bg-white border border-gray-300 rounded-md shadow-lg max-h-40 overflow-y-auto w-full"></div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Produto</label>
                            <input type="text" class="product-name w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none" readonly>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Variação</label>
                            <input type="text" class="variation-info w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none" readonly>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade <span class="text-red-500">*</span></label>
                            <input type="number" name="items[${itemCount}][quantity]" class="quantity w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="1" min="1" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Preço Unitário (R$) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="items[${itemCount}][unit_price]" class="unit-price w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="0" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal</label>
                            <input type="text" class="subtotal w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none" readonly>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estoque Atual</label>
                            <input type="text" class="current-stock w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none" readonly>
                        </div>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('afterbegin', newItemHtml);
            const newItem = container.firstElementChild;

            setupSkuSearch(newItem);
            setupNewItemEventListeners(newItem);

            itemCount++;
        }

        function setupNewItemEventListeners(container) {
            container.querySelector('.remove-item').addEventListener('click', function() {
                container.remove();
                calculateTotal();
            });

            container.querySelector('.quantity').addEventListener('input', function() {
                calculateSubtotal(container);
                calculateTotal();
            });

            container.querySelector('.unit-price').addEventListener('input', function() {
                calculateSubtotal(container);
                calculateTotal();
            });
        }

        function removeExistingItem(itemId) {
            if (!confirm('Tem certeza que deseja remover este item da compra?')) {
                return;
            }

            // Esconder o item visualmente
            const itemRow = document.getElementById('existing-item-' + itemId);
            if (itemRow) {
                itemRow.style.display = 'none';
                itemRow.classList.add('item-to-delete');
            }

            // Adicionar campo hidden para marcar para deletar
            const deleteInput = document.createElement('input');
            deleteInput.type = 'hidden';
            deleteInput.name = 'items_to_delete[]';
            deleteInput.value = itemId;
            document.getElementById('purchase-form').appendChild(deleteInput);

            // Recalcular total
            calculateTotal();
        }

        function setupExistingItemEventListeners(container) {
            // Quantity and price changes
            const quantityInput = container.querySelector('.existing-quantity');
            const unitPriceInput = container.querySelector('.existing-unit-price');

            if (quantityInput) {
                quantityInput.addEventListener('input', function() {
                    calculateSubtotal(container);
                    calculateTotal();
                });
            }

            if (unitPriceInput) {
                unitPriceInput.addEventListener('input', function() {
                    calculateSubtotal(container);
                    calculateTotal();
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.existing-item-row').forEach(item => {
                setupExistingItemEventListeners(item);
                calculateSubtotal(item);
            });

            document.getElementById('add-item').addEventListener('click', addNewItem);

            document.getElementById('installments').addEventListener('input', function() {
                calculateTotal();
            });

            calculateTotal();
        });
    </script>
@endsection
