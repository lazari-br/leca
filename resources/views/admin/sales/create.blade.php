@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="mb-6">
            <a href="{{ route('admin.sales.index') }}" class="text-blue-500 hover:text-blue-700">
                &larr; Voltar para lista de vendas
            </a>
            <h1 class="text-2xl font-bold text-gray-800 mt-2">Registrar Venda</h1>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form method="POST" action="{{ route('admin.sales.store') }}" id="sales-form">
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

                <!-- Dados da Venda -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Dados da Venda</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Cliente <span class="text-red-500">*</span></label>
                            <input type="text" name="customer_name" id="customer_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nome do cliente" value="{{ old('customer_name') }}" required>
                            @error('customer_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="sale_date" class="block text-sm font-medium text-gray-700 mb-1">Data da Venda <span class="text-red-500">*</span></label>
                            <input type="date" name="sale_date" id="sale_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('sale_date', date('Y-m-d')) }}" required>
                            @error('sale_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-1">Data de Pagamento <span class="text-red-500">*</span></label>
                            <input type="date" name="payment_date" id="payment_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                            @error('payment_date')
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
                            <input type="text" id="installment_value" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none" readonly>
                        </div>

                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Método de Pagamento <span class="text-red-500">*</span></label>
                            <select name="payment_method" id="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Selecione o método</option>
                                <option value="PIX" {{ old('payment_method') == 'PIX' ? 'selected' : '' }}>PIX</option>
                                <option value="Cartão de Crédito" {{ old('payment_method') == 'Cartão de Crédito' ? 'selected' : '' }}>Cartão de Crédito</option>
                                <option value="Cartão de Débito" {{ old('payment_method') == 'Cartão de Débito' ? 'selected' : '' }}>Cartão de Débito</option>
                                <option value="Dinheiro" {{ old('payment_method') == 'Dinheiro' ? 'selected' : '' }}>Dinheiro</option>
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
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Estoque Disponível</label>
                                        <input type="text" class="current-stock w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none" readonly value="{{ $item['current_stock'] ?? '' }}">
                                        <div class="stock-warning text-xs mt-1 hidden"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @error('items')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total e Comissão -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total da Venda</label>
                            <input type="text" id="total_display" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none text-lg font-semibold" readonly>
                            <input type="hidden" name="total" id="total_value" value="{{ old('total') }}">
                            <input type="hidden" name="installment_value" id="installment_value_for_validation">
                        </div>

                        @if(auth()->user()->user_type === 'vendedor')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Valor da Comissão ({{ auth()->user()->commission ?? 0 }}%)</label>
                                <input type="text" id="commission_display" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none text-lg font-semibold" readonly>
                                <input type="hidden" name="commission_value" id="commission_value" value="0">
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-md focus:outline-none focus:shadow-outline transition-colors">
                        Registrar Venda
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let itemCount = {{ count(old('items', [1])) }};
        let debounceTimeout = null;

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
            if (!colorValue.startsWith('#')) return colorValue;
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
                    'product_price' => $variation->product->price ?? 0
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
                    const colorName = getColorName(variation.color);
                    variationInfoInput.value = variation.size + (colorName ? ' - ' + colorName : '');
                    currentStockInput.value = variation.stock;
                    if (!unitPriceInput.value) {
                        unitPriceInput.value = parseFloat(variation.product_price).toFixed(2);
                    }
                }
            }

            skuInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();

                // Debounce para melhor performance
                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(() => {
                    if (searchTerm.length < 1) {
                        suggestionsDiv.classList.add('hidden');
                        return;
                    }

                    // Filtrar apenas produtos com estoque > 0
                    const matches = allVariations.filter(variation =>
                            variation.stock > 0 && (
                                variation.code.toLowerCase().includes(searchTerm) ||
                                variation.product_name.toLowerCase().includes(searchTerm)
                            )
                    );

                    if (matches.length > 0) {
                        let suggestionsHtml = '';
                        matches.forEach(variation => {
                            const variationJson = JSON.stringify(variation).replace(/"/g, '&quot;');
                            const colorName = getColorName(variation.color);
                            const displayColor = colorName || variation.color;
                            const stockClass = variation.stock <= 5 ? 'text-red-500' : variation.stock <= 10 ? 'text-yellow-500' : 'text-green-500';

                            suggestionsHtml += `
                                <div class="suggestion-item px-3 py-2 hover:bg-gray-100 cursor-pointer border-b" data-variation="${variationJson}">
                                    <div class="font-medium">${variation.code}</div>
                                    <div class="text-sm text-gray-600">${variation.product_name} - ${variation.size}${displayColor ? ' (' + displayColor + ')' : ''}</div>
                                    <div class="text-xs ${stockClass}">Estoque: ${variation.stock} | Preço: R$ ${parseFloat(variation.product_price).toFixed(2).replace('.', ',')}</div>
                                </div>
                            `;
                        });
                        suggestionsDiv.innerHTML = suggestionsHtml;
                        suggestionsDiv.classList.remove('hidden');
                    } else {
                        suggestionsDiv.innerHTML = '<div class="px-3 py-2 text-gray-500">Nenhum SKU com estoque encontrado</div>';
                        suggestionsDiv.classList.remove('hidden');
                    }
                }, 300);
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

                    const colorName = getColorName(variation.color);
                    variationInfoInput.value = variation.size + (colorName ? ' - ' + colorName : '');

                    unitPriceInput.value = parseFloat(variation.product_price).toFixed(2);
                    currentStockInput.value = variation.stock;

                    suggestionsDiv.classList.add('hidden');
                    updateStockWarning(container, variation.stock);
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

        function updateStockWarning(container, stock) {
            const quantityInput = container.querySelector('.quantity');
            const warningDiv = container.querySelector('.stock-warning');
            const quantity = parseInt(quantityInput.value) || 0;

            warningDiv.classList.add('hidden');
            quantityInput.classList.remove('border-red-500', 'border-yellow-500');

            if (quantity > stock) {
                warningDiv.textContent = `⚠️ Quantidade maior que estoque disponível (${stock})`;
                warningDiv.classList.remove('hidden');
                warningDiv.className = warningDiv.className.replace(/text-\w+-500/, 'text-red-500');
                quantityInput.classList.add('border-red-500');
            } else if (quantity > stock * 0.8) {
                warningDiv.textContent = `⚠️ Quantidade alta - restam apenas ${stock - quantity} unidades`;
                warningDiv.classList.remove('hidden');
                warningDiv.className = warningDiv.className.replace(/text-\w+-500/, 'text-yellow-500');
                quantityInput.classList.add('border-yellow-500');
            }
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

            @if(auth()->user()->user_type === 'vendedor')
            calculateCommission(total);
            @endif
        }

        function calculateInstallmentValue(total) {
            const installments = parseInt(document.getElementById('installments').value) || 1;
            const value = (total / installments).toFixed(2);
            document.getElementById('installment_value').value = 'R$ ' + value.replace('.', ',');
            document.getElementById('installment_value_for_validation').value = value;
        }

        @if(auth()->user()->user_type === 'vendedor')
        function calculateCommission(total) {
            const commissionRate = {{ auth()->user()->commission ?? 0 }} / 100;
            const commissionValue = total * commissionRate;
            document.getElementById('commission_display').value = 'R$ ' + commissionValue.toFixed(2).replace('.', ',');
            document.getElementById('commission_value').value = commissionValue.toFixed(2);
        }
        @endif

        function updateItemIndices() {
            document.querySelectorAll('.item-row').forEach((row, index) => {
                row.querySelector('h4').textContent = `SKU #${index + 1}`;
                row.querySelector('.product-id').name = `items[${index}][product_id]`;
                row.querySelector('.variation-id').name = `items[${index}][product_variation_id]`;
                row.querySelector('.quantity').name = `items[${index}][quantity]`;
                row.querySelector('.unit-price').name = `items[${index}][unit_price]`;

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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estoque Disponível</label>
                            <input type="text" class="current-stock w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none" readonly>
                            <div class="stock-warning text-xs mt-1 hidden"></div>
                        </div>
                    </div>
                </div>
            `;

            // Inserir no início do container (primeiro item)
            container.insertAdjacentHTML('afterbegin', newItemHtml);
            const newItem = container.firstElementChild;

            setupSkuSearch(newItem);
            setupItemEventListeners(newItem);

            itemCount++;
            updateItemIndices();
        }

        function setupItemEventListeners(container) {
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

            const quantityInput = container.querySelector('.quantity');
            const unitPriceInput = container.querySelector('.unit-price');

            quantityInput.addEventListener('input', function() {
                const stockInput = container.querySelector('.current-stock');
                const stock = parseInt(stockInput.value) || 0;
                updateStockWarning(container, stock);
                calculateSubtotal(container);
                calculateTotal();
            });

            unitPriceInput.addEventListener('input', function() {
                calculateSubtotal(container);
                calculateTotal();
            });
        }

        function validateForm() {
            let isValid = true;
            let stockErrors = [];

            // Validar campos principais
            const requiredFields = ['customer_name', 'sale_date', 'payment_date', 'installments', 'payment_method'];
            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field && !field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                } else if (field) {
                    field.classList.remove('border-red-500');
                }
            });

            // Validar SKUs
            document.querySelectorAll('.item-row').forEach((row, index) => {
                const variationId = row.querySelector('.variation-id');
                const quantity = row.querySelector('.quantity');
                const unitPrice = row.querySelector('.unit-price');
                const currentStock = row.querySelector('.current-stock');
                const skuSearch = row.querySelector('.sku-search');

                // Validar se SKU foi selecionado
                if (!variationId.value.trim()) {
                    isValid = false;
                    skuSearch.classList.add('border-red-500');
                } else {
                    skuSearch.classList.remove('border-red-500');
                }

                // Validar quantidade
                if (!quantity.value.trim() || quantity.value <= 0) {
                    isValid = false;
                    quantity.classList.add('border-red-500');
                } else {
                    quantity.classList.remove('border-red-500');

                    // Validar estoque
                    const stock = parseInt(currentStock.value) || 0;
                    const qty = parseInt(quantity.value) || 0;
                    if (qty > stock) {
                        isValid = false;
                        quantity.classList.add('border-red-500');
                        stockErrors.push(`SKU #${index + 1}: Quantidade (${qty}) maior que estoque disponível (${stock})`);
                    }
                }

                // Validar preço
                if (!unitPrice.value.trim() || unitPrice.value <= 0) {
                    isValid = false;
                    unitPrice.classList.add('border-red-500');
                } else {
                    unitPrice.classList.remove('border-red-500');
                }
            });

            // Mostrar erros específicos de estoque
            if (stockErrors.length > 0) {
                alert('Problemas de estoque encontrados:\n\n' + stockErrors.join('\n'));
            } else if (!isValid) {
                alert('Por favor, preencha todos os campos obrigatórios e verifique se todos os SKUs foram selecionados corretamente.');
            }

            return isValid;
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

            // Validação no submit
            document.getElementById('sales-form').addEventListener('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                    return false;
                }
            });

            // Calcular total inicial
            calculateTotal();
        });
    </script>
@endsection
