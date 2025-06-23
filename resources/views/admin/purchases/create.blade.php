@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="mb-6">
            <a href="{{ route('admin.purchases.index') }}" class="text-blue-500 hover:text-blue-700">
                &larr; Voltar para lista de compras
            </a>
            <h1 class="text-2xl font-bold text-gray-800 mt-2">Registrar Compra</h1>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form method="POST" action="{{ route('admin.purchases.store') }}" id="purchase-form">
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

                <!-- Dados da Compra -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Dados da Compra</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="supplier_name" class="block text-sm font-medium text-gray-700 mb-1">Fornecedor <span class="text-red-500">*</span></label>
                            <input type="text" name="supplier_name" id="supplier_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nome do fornecedor" value="{{ old('supplier_name') }}" required>
                            @error('supplier_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="purchase_date" class="block text-sm font-medium text-gray-700 mb-1">Data da Compra <span class="text-red-500">*</span></label>
                            <input type="date" name="purchase_date" id="purchase_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                            @error('purchase_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="installments" class="block text-sm font-medium text-gray-700 mb-1">Parcelas <span class="text-red-500">*</span></label>
                            <input type="number" name="installments" id="installments" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Qtd de parcelas" value="{{ old('installments', 1) }}" min="1" required>
                            @error('installments')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="installment_value" class="block text-sm font-medium text-gray-700 mb-1">Valor da Parcela</label>
                            <input type="text" name="installment_value" id="installment_value" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none" readonly>
                            <input type="hidden" name="installment_value_numeric" id="installment_value_numeric" value="{{ old('installment_value') }}">
                        </div>

                        <div class="md:col-span-2">
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Método de Pagamento <span class="text-red-500">*</span></label>
                            <select name="payment_method" id="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Selecione o método</option>
                                <option value="PIX" {{ old('payment_method') == 'PIX' ? 'selected' : '' }}>PIX</option>
                                <option value="Cartão de Crédito" {{ old('payment_method') == 'Cartão de Crédito' ? 'selected' : '' }}>Cartão de Crédito</option>
                                <option value="Cartão de Débito" {{ old('payment_method') == 'Cartão de Débito' ? 'selected' : '' }}>Cartão de Débito</option>
                                <option value="Dinheiro" {{ old('payment_method') == 'Dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                                <option value="Boleto" {{ old('payment_method') == 'Boleto' ? 'selected' : '' }}>Boleto</option>
                            </select>
                            @error('payment_method')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- SKUs/Produtos -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">SKUs / Produtos <span class="text-red-500">*</span></h2>
                        <button type="button" id="add-item" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">Adicionar SKU</button>
                    </div>

                    <div id="items-container">
                        @php
                            $oldItems = old('items', [[]]);
                        @endphp

                        @foreach($oldItems as $index => $item)
                            <div class="bg-gray-50 p-4 rounded-lg mb-4 item-row">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="font-medium text-gray-700">SKU #{{ $index + 1 }}</h4>
                                    @if($index > 0)
                                        <button type="button" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm remove-item">Remover</button>
                                    @else
                                        <button type="button" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm remove-item hidden">Remover</button>
                                    @endif
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div class="relative">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Buscar SKU <span class="text-red-500">*</span></label>
                                        <input type="text" class="sku-search w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Digite o código do SKU" value="{{ $item['sku_code'] ?? '' }}" required>
                                        <input type="hidden" name="items[{{ $index }}][product_id]" class="product-id" value="{{ $item['product_id'] ?? '' }}" required>
                                        <input type="hidden" name="items[{{ $index }}][product_variation_id]" class="variation-id" value="{{ $item['product_variation_id'] ?? '' }}" required>
                                        <div class="sku-suggestions hidden absolute z-10 bg-white border border-gray-300 rounded-md shadow-lg max-h-40 overflow-y-auto w-full"></div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Produto</label>
                                        <input type="text" class="product-name w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none" readonly placeholder="Nome do produto" value="{{ $item['product_name'] ?? '' }}">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Variação</label>
                                        <input type="text" class="variation-info w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none" readonly placeholder="Tamanho e cor" value="{{ $item['variation_info'] ?? '' }}">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade <span class="text-red-500">*</span></label>
                                        <input type="number" name="items[{{ $index }}][quantity]" class="quantity w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $item['quantity'] ?? 1 }}" min="1" required>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Preço Unitário (R$) <span class="text-red-500">*</span></label>
                                        <input type="number" step="0.01" name="items[{{ $index }}][unit_price]" class="unit-price w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="0" value="{{ $item['unit_price'] ?? '' }}" required>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal</label>
                                        <input type="text" class="subtotal w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none" readonly>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Estoque Atual</label>
                                        <input type="text" class="current-stock w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none" readonly value="{{ $item['current_stock'] ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @error('items')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total da Compra</label>
                            <input type="text" id="total_display" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none text-lg font-semibold" readonly>
                            <input type="hidden" name="total" id="total_value" value="{{ old('total') }}">
                            <input type="hidden" name="installment_value" id="installment_value_for_validation">
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-md focus:outline-none focus:shadow-outline transition-colors">
                        Registrar Compra
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let itemCount = {{ count(old('items', [1])) }};

        // Variações disponíveis - obtidas do backend
        let allVariations = [];

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

            // Carregar dados do variation_id se existir
            if (variationIdInput.value) {
                const variation = allVariations.find(v => v.id == variationIdInput.value);
                if (variation) {
                    skuInput.value = variation.code;
                    productIdInput.value = variation.product_id;
                    productNameInput.value = variation.product_name;
                    variationInfoInput.value = variation.size + (variation.color ? ' - ' + variation.color : '');
                    currentStockInput.value = variation.stock;
                    if (!unitPriceInput.value) {
                        unitPriceInput.value = parseFloat(variation.product_purchase_price).toFixed(2);
                    }
                }
            }

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
                        suggestionsHtml += `
                            <div class="suggestion-item px-3 py-2 hover:bg-gray-100 cursor-pointer border-b" data-variation="${variationJson}">
                                <div class="font-medium">${variation.code}</div>
                                <div class="text-sm text-gray-600">${variation.product_name} - ${variation.size}${variation.color ? ' (' + variation.color + ')' : ''}</div>
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
                    variationInfoInput.value = variation.size + (variation.color ? ' - ' + variation.color : '');
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
            const quantity = parseFloat(container.querySelector('.quantity').value) || 0;
            const unitPrice = parseFloat(container.querySelector('.unit-price').value) || 0;
            const subtotal = quantity * unitPrice;

            container.querySelector('.subtotal').value = 'R$ ' + subtotal.toFixed(2).replace('.', ',');
        }

        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.item-row').forEach(row => {
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
            document.getElementById('installment_value_numeric').value = value;
            document.getElementById('installment_value_for_validation').value = value;
        }

        function updateItemIndices() {
            document.querySelectorAll('.item-row').forEach((row, index) => {
                // Atualizar título
                row.querySelector('h4').textContent = `SKU #${index + 1}`;

                // Atualizar nomes dos inputs
                row.querySelector('.product-id').name = `items[${index}][product_id]`;
                row.querySelector('.variation-id').name = `items[${index}][product_variation_id]`;
                row.querySelector('.quantity').name = `items[${index}][quantity]`;
                row.querySelector('.unit-price').name = `items[${index}][unit_price]`;

                // Mostrar/esconder botão de remover
                const removeBtn = row.querySelector('.remove-item');
                if (index === 0) {
                    removeBtn.classList.add('hidden');
                } else {
                    removeBtn.classList.remove('hidden');
                }
            });
        }

        function addNewItem() {
            const container = document.getElementById('items-container');

            const newItemHtml = `
                <div class="bg-gray-50 p-4 rounded-lg mb-4 item-row">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-medium text-gray-700">SKU #${itemCount + 1}</h4>
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
                            <input type="text" class="product-name w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none" readonly placeholder="Nome do produto">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Variação</label>
                            <input type="text" class="variation-info w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none" readonly placeholder="Tamanho e cor">
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

            // Inserir no início do container (primeiro item)
            container.insertAdjacentHTML('afterbegin', newItemHtml);
            const newItem = container.firstElementChild;

            // Setup novo search
            setupSkuSearch(newItem);

            // Event listeners para novo item
            setupItemEventListeners(newItem);

            itemCount++;
            updateItemIndices();
        }

        function setupItemEventListeners(container) {
            // Remove button
            const removeBtn = container.querySelector('.remove-item');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    if (document.querySelectorAll('.item-row').length > 1) {
                        container.remove();
                        updateItemIndices();
                        calculateTotal();
                    }
                });
            }

            // Quantity and price changes
            container.querySelector('.quantity').addEventListener('input', function() {
                calculateSubtotal(container);
                calculateTotal();
            });

            container.querySelector('.unit-price').addEventListener('input', function() {
                calculateSubtotal(container);
                calculateTotal();
            });
        }

        // Event listeners principais
        document.addEventListener('DOMContentLoaded', function() {
            // Setup todos os itens existentes
            document.querySelectorAll('.item-row').forEach(item => {
                setupSkuSearch(item);
                setupItemEventListeners(item);
            });

            // Add item button
            document.getElementById('add-item').addEventListener('click', addNewItem);

            // Installments change
            document.getElementById('installments').addEventListener('input', function() {
                calculateTotal();
            });

            // Calcular total inicial
            calculateTotal();
        });
    </script>
@endsection
