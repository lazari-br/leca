@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">Editar Venda</h1>

        <form method="POST" action="{{ route('admin.sales.update', $sale->id) }}" id="sales-form">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="customer_name" class="block font-medium">Cliente <span class="text-red-500">*</span></label>
                    <input type="text" name="customer_name" id="customer_name" class="w-full border-gray-300 rounded" placeholder="Nome do cliente" value="{{ old('customer_name', $sale->customer_name) }}" required>
                    <div class="error-message text-red-500 text-sm mt-1 hidden">O campo cliente é obrigatório.</div>
                </div>

                <div>
                    <label for="sale_date" class="block font-medium">Data da Venda <span class="text-red-500">*</span></label>
                    <input type="text" name="sale_date" id="sale_date" class="w-full border-gray-300 rounded flatpickr placeholder-gray-400" placeholder="Selecione a data" value="{{ old('sale_date', $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('d-m-Y') : '') }}" required>
                    <div class="error-message text-red-500 text-sm mt-1 hidden">O campo data da venda é obrigatório.</div>
                </div>

                <div>
                    <label for="payment_date" class="block font-medium">Data de Pagamento <span class="text-red-500">*</span></label>
                    <input type="text" name="payment_date" id="payment_date" class="w-full border-gray-300 rounded flatpickr placeholder-gray-400" placeholder="Selecione a data" value="{{ old('payment_date', $sale->payment_date ? \Carbon\Carbon::parse($sale->payment_date)->format('d-m-Y') : '') }}" required>
                    <div class="error-message text-red-500 text-sm mt-1 hidden">O campo data de pagamento é obrigatório.</div>
                </div>

                <div>
                    <label for="installments" class="block font-medium">Parcelas <span class="text-red-500">*</span></label>
                    <input type="number" name="installments" id="installments" class="w-full border-gray-300 rounded" placeholder="Qtd de parcelas" value="{{ old('installments', $sale->installments) }}" min="1" required>
                    <div class="error-message text-red-500 text-sm mt-1 hidden">O campo parcelas é obrigatório.</div>
                </div>

                <div>
                    <label for="installment_value" class="block font-medium">Valor da Parcela</label>
                    <input type="number" step="0.01" name="installment_value" id="installment_value" class="w-full border-gray-300 rounded bg-gray-100" readonly>
                </div>

                <div>
                    <label for="payment_method" class="block font-medium">Método de Pagamento <span class="text-red-500">*</span></label>
                    <select name="payment_method" id="payment_method" class="w-full border-gray-300 rounded" required>
                        <option value="">Selecione o método</option>
                        <option value="PIX" {{ old('payment_method', $sale->payment_method) == 'PIX' ? 'selected' : '' }}>PIX</option>
                        <option value="Cartão de Crédito" {{ old('payment_method', $sale->payment_method) == 'Cartão de Crédito' ? 'selected' : '' }}>Cartão de Crédito</option>
                        <option value="Cartão de Débito" {{ old('payment_method', $sale->payment_method) == 'Cartão de Débito' ? 'selected' : '' }}>Cartão de Débito</option>
                        <option value="Dinheiro" {{ old('payment_method', $sale->payment_method) == 'Dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                    </select>
                    <div class="error-message text-red-500 text-sm mt-1 hidden">O campo método de pagamento é obrigatório.</div>
                </div>
            </div>

            <h2 class="text-xl font-semibold mt-6 mb-2">Produtos</h2>

            <div id="items-container">
                @foreach ($sale->items as $index => $item)
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 item-row mb-4">
                        <div>
                            <label class="block font-medium">Código <span class="text-red-500">*</span></label>
                            <select name="items[{{ $index }}][product_id]" class="product-select w-full border rounded" data-index="{{ $index }}" required>
                                <option value="">Selecione código</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}"
                                            data-variations='@json($product->variations)'
                                            data-price="{{ $product->price }}"
                                            data-name="{{ $product->name }}"
                                        {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                        {{ $product->code ?? $product->id }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="error-message text-red-500 text-sm mt-1 hidden">O campo código é obrigatório.</div>
                        </div>

                        <div>
                            <label class="block font-medium">Produto</label>
                            <input type="text" class="product-name w-full border-gray-300 rounded bg-gray-100" placeholder="Nome do produto" readonly value="{{ $item->product->name ?? '' }}">
                        </div>

                        <div>
                            <label class="block font-medium">Variação <span class="text-red-500">*</span></label>
                            <select name="items[{{ $index }}][product_variation_id]" id="variation-{{ $index }}" class="variation-select w-full border rounded" data-index="{{ $index }}" required>
                                <option value="">Selecione uma variação</option>
                                @foreach ($products->firstWhere('id', $item->product_id)?->variations ?? [] as $variation)
                                    <option value="{{ $variation->id }}" {{ $item->product_variation_id == $variation->id ? 'selected' : '' }}>
                                        {{ $variation->size }} ({{ $variation->color }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="error-message text-red-500 text-sm mt-1 hidden">O campo variação é obrigatório.</div>
                        </div>

                        <div>
                            <label class="block font-medium">Quantidade <span class="text-red-500">*</span></label>
                            <input type="number" name="items[{{ $index }}][quantity]" class="w-full border-gray-300 rounded quantity" value="{{ $item->quantity }}" min="1" required>
                            <div class="error-message text-red-500 text-sm mt-1 hidden">O campo quantidade é obrigatório.</div>
                        </div>

                        <div>
                            <label class="block font-medium">Valor Unitário</label>
                            <input type="number" step="0.01" name="items[{{ $index }}][unit_price]" class="w-full border-gray-300 rounded unit-price bg-gray-100" value="{{ $item->unit_price }}" readonly>
                        </div>

                        <div class="flex items-end">
                            <button type="button" class="bg-red-500 text-white px-3 py-2 rounded remove-item {{ $loop->first ? 'hidden' : '' }}">Remover</button>
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="button" id="add-item" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Adicionar Produto</button>

            <div class="mt-6">
                <label class="block font-medium">Total</label>
                <input type="text" id="total" name="total_display" class="w-full border-gray-300 rounded bg-gray-100" readonly>
            </div>

            @if(auth()->user()->user_type === 'vendedor')
                <div class="mt-6">
                    <label class="block font-medium">Valor da Comissão ({{ auth()->user()->commission }}%)</label>
                    <input type="text" id="commission_value" name="commission_display" class="w-full border-gray-300 rounded bg-gray-100" readonly>
                    <input type="hidden" name="commission_value" id="commission_value_hidden" value="{{ $sale->commission_value ?? 0 }}">
                </div>
            @endif

            <div class="mt-6">
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded">Atualizar Venda</button>
            </div>
        </form>
    </div>

    @push('scripts')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                flatpickr(".flatpickr", { dateFormat: "d-m-Y" });
            });

            function hexToColorName(hex) {
                const map = {
                    '#000000': 'preto', '#ffffff': 'branco', '#ff0000': 'vermelho',
                    '#00ff00': 'verde', '#0000ff': 'azul', '#ffff00': 'amarelo',
                    '#ffa500': 'laranja', '#800080': 'roxo', '#ffc0cb': 'rosa'
                };
                return map[hex.toLowerCase()] || hex;
            }

            function populateVariations(productSelect) {
                const index = productSelect.dataset.index;
                const option = productSelect.options[productSelect.selectedIndex];
                const variations = JSON.parse(option.getAttribute('data-variations') || '[]');
                const variationSelect = document.getElementById(`variation-${index}`);
                const unitPriceInput = productSelect.closest('.item-row').querySelector('.unit-price');
                const productNameInput = productSelect.closest('.item-row').querySelector('.product-name');

                // Atualizar nome do produto
                const productName = option.getAttribute('data-name') || '';
                productNameInput.value = productName;

                variationSelect.innerHTML = '<option value="">Selecione uma variação</option>';
                variations.forEach(v => {
                    const opt = document.createElement('option');
                    opt.value = v.id;
                    opt.textContent = `${v.size} (${hexToColorName(v.color)})`;
                    variationSelect.appendChild(opt);
                });

                const price = parseFloat(option.getAttribute('data-price') || 0).toFixed(2);
                unitPriceInput.value = price;
                calculateTotalAndInstallments();
            }

            function calculateInstallmentValue(total) {
                const installments = parseInt(document.getElementById('installments').value) || 1;
                const value = (total / installments).toFixed(2);
                document.getElementById('installment_value').value = value;
            }

            function calculateTotalAndInstallments() {
                let total = 0;
                document.querySelectorAll('.item-row').forEach(row => {
                    const quantity = parseFloat(row.querySelector('.quantity')?.value || 0);
                    const unit = parseFloat(row.querySelector('.unit-price')?.value || 0);
                    total += quantity * unit;
                });
                document.getElementById('total').value = `R$ ${total.toFixed(2).replace('.', ',')}`;
                calculateInstallmentValue(total);

                // Calcular comissão se for vendedor
                @if(auth()->user()->user_type === 'vendedor')
                calculateCommission(total);
                @endif
            }

            @if(auth()->user()->user_type === 'vendedor')
            function calculateCommission(total) {
                const commissionRate = {{ auth()->user()->commission ?? 0 }} / 100;
                const commissionValue = total * commissionRate;
                document.getElementById('commission_value').value = `R$ ${commissionValue.toFixed(2).replace('.', ',')}`;
                document.getElementById('commission_value_hidden').value = commissionValue.toFixed(2);
            }

            // Calcular comissão inicial ao carregar a página
            window.addEventListener('load', () => {
                const initialTotal = {{ $sale->total ?? 0 }};
                if (initialTotal > 0) {
                    calculateCommission(initialTotal);
                }
            });
            @endif

            function validateField(field) {
                const isValid = field.value.trim() !== '';
                const errorMessage = field.parentElement.querySelector('.error-message');

                if (!isValid) {
                    field.classList.add('border-red-500');
                    field.classList.remove('border-gray-300');
                    if (errorMessage) errorMessage.classList.remove('hidden');
                } else {
                    field.classList.remove('border-red-500');
                    field.classList.add('border-gray-300');
                    if (errorMessage) errorMessage.classList.add('hidden');
                }

                return isValid;
            }

            function validateForm() {
                let isValid = true;

                // Validar campos principais
                const requiredFields = ['customer_name', 'sale_date', 'payment_date', 'installments', 'payment_method'];
                requiredFields.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (field && !validateField(field)) {
                        isValid = false;
                    }
                });

                // Validar produtos
                document.querySelectorAll('.item-row').forEach(row => {
                    const productSelect = row.querySelector('.product-select');
                    const variationSelect = row.querySelector('.variation-select');
                    const quantityInput = row.querySelector('.quantity');

                    if (!validateField(productSelect)) isValid = false;
                    if (!validateField(variationSelect)) isValid = false;
                    if (!validateField(quantityInput)) isValid = false;
                });

                return isValid;
            }

            // Event listeners para validação em tempo real
            document.addEventListener('blur', (e) => {
                if (e.target.hasAttribute('required')) {
                    validateField(e.target);
                }
            }, true);

            document.addEventListener('change', (e) => {
                if (e.target.hasAttribute('required')) {
                    validateField(e.target);
                }
            }, true);

            // Validação no submit
            document.getElementById('sales-form').addEventListener('submit', (e) => {
                // Garantir que todos os unit_price tenham valores válidos
                document.querySelectorAll('.unit-price').forEach(input => {
                    if (!input.value || input.value === '' || input.value === '0') {
                        const productSelect = input.closest('.item-row').querySelector('.product-select');
                        if (productSelect.value) {
                            const option = productSelect.options[productSelect.selectedIndex];
                            const price = parseFloat(option.getAttribute('data-price') || 0).toFixed(2);
                            input.value = price;
                        }
                    }
                });

                if (!validateForm()) {
                    e.preventDefault();
                    alert('Por favor, preencha todos os campos obrigatórios.');
                }
            });

            window.addEventListener('load', () => {
                document.querySelectorAll('.product-select').forEach(select => {
                    select.addEventListener('change', () => populateVariations(select));
                });

                document.getElementById('installments').addEventListener('input', calculateTotalAndInstallments);
                document.addEventListener('input', () => {
                    calculateTotalAndInstallments();
                });

                // Calcular total inicial
                calculateTotalAndInstallments();
            });

            let itemCount = {{ $sale->items->count() }};
            document.getElementById('add-item').addEventListener('click', () => {
                const container = document.getElementById('items-container');
                const original = container.querySelector('.item-row');
                const clone = original.cloneNode(true);

                clone.querySelectorAll('input, select').forEach(el => {
                    const name = el.getAttribute('name');
                    if (name) el.setAttribute('name', name.replace(/items\[\d+\]/, `items[${itemCount}]`));

                    // Limpar valores, exceto quantidade que fica 1
                    if (el.classList.contains('quantity')) {
                        el.value = '1';
                    } else if (el.classList.contains('unit-price')) {
                        el.value = '0'; // Valor padrão para unit_price
                    } else {
                        el.value = '';
                    }

                    // Limpar validação do clone
                    el.classList.remove('border-red-500');
                    el.classList.add('border-gray-300');
                });

                // Limpar mensagens de erro do clone
                clone.querySelectorAll('.error-message').forEach(msg => msg.classList.add('hidden'));

                clone.querySelector('.remove-item').classList.remove('hidden');
                clone.querySelector('.remove-item').addEventListener('click', () => {
                    clone.remove();
                    calculateTotalAndInstallments();
                });
                clone.querySelector('.product-select').setAttribute('data-index', itemCount);
                clone.querySelector('.variation-select').setAttribute('id', `variation-${itemCount}`);

                container.appendChild(clone);

                const newSelect = clone.querySelector('.product-select');
                newSelect.addEventListener('change', () => populateVariations(newSelect));

                itemCount++;
            });

            // Event listeners para botões de remover existentes
            document.querySelectorAll('.remove-item').forEach(button => {
                button.addEventListener('click', function () {
                    this.closest('.item-row').remove();
                    calculateTotalAndInstallments();
                });
            });
        </script>
    @endpush
@endsection
