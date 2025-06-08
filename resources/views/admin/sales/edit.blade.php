@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">Editar Venda</h1>

        <form method="POST" action="{{ route('admin.sales.update', $sale->id) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="customer_name" class="block font-medium">Cliente</label>
                    <input type="text" name="customer_name" id="customer_name" class="w-full border-gray-300 rounded" value="{{ old('customer_name', $sale->customer_name) }}">
                </div>

                <div>
                    <label for="sale_date" class="block font-medium">Data da Venda</label>
                    <input type="date" name="sale_date" id="sale_date" class="w-full border-gray-300 rounded" value="{{ old('sale_date', $sale->sale_date->format('Y-m-d')) }}">
                </div>

                <div>
                    <label for="installments" class="block font-medium">Parcelas</label>
                    <input type="number" name="installments" id="installments" class="w-full border-gray-300 rounded" value="{{ old('installments', $sale->installments) }}">
                </div>

                <div>
                    <label for="installment_value" class="block font-medium">Valor da Parcela</label>
                    <input type="number" step="0.01" name="installment_value" id="installment_value" class="w-full border-gray-300 rounded" value="{{ old('installment_value', $sale->installment_value) }}">
                </div>
            </div>

            <h2 class="text-xl font-semibold mt-6 mb-2">Produtos</h2>

            <div id="items-container">
                @foreach ($sale->items as $index => $item)
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 item-row">
                        <div>
                            <label class="block font-medium">Produto</label>
                            <select name="items[{{ $index }}][product_id]" class="w-full border-gray-300 rounded">
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-medium">Variação</label>
                            <select name="items[{{ $index }}][product_variation_id]" class="w-full border-gray-300 rounded">
                                @foreach ($products->firstWhere('id', $item->product_id)?->variations ?? [] as $variation)
                                    <option value="{{ $variation->id }}" {{ $item->product_variation_id == $variation->id ? 'selected' : '' }}>
                                        {{ $variation->size }} / {{ $variation->color }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-medium">Quantidade</label>
                            <input type="number" name="items[{{ $index }}][quantity]" class="w-full border-gray-300 rounded" value="{{ $item->quantity }}">
                        </div>

                        <div>
                            <label class="block font-medium">Valor Unitário</label>
                            <input type="number" step="0.01" name="items[{{ $index }}][unit_price]" class="w-full border-gray-300 rounded" value="{{ $item->unit_price }}">
                        </div>

                        <div class="flex items-end">
                            <button type="button" class="bg-red-500 text-white px-3 py-2 rounded remove-item">Remover</button>
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="button" id="add-item" class="mt-4 bg-green-500 text-white px-4 py-2 rounded">Adicionar Produto</button>

            <div class="mt-6">
                <label class="block font-medium">Total</label>
                <input type="text" id="total" name="total_display" class="w-full border-gray-300 rounded bg-gray-100" readonly>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">Atualizar Venda</button>
            </div>
        </form>
    </div>

    <script>
        let itemIndex = {{ $sale->items->count() }};

        document.getElementById('add-item').addEventListener('click', () => {
            const container = document.getElementById('items-container');
            const newRow = container.firstElementChild.cloneNode(true);

            newRow.querySelectorAll('input, select').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    const newName = name.replace(/items\[\d+\]/, `items[${itemIndex}]`);
                    input.setAttribute('name', newName);
                }
                if (input.tagName === 'INPUT') input.value = '';
            });

            newRow.querySelector('.remove-item').addEventListener('click', () => newRow.remove());
            container.appendChild(newRow);
            itemIndex++;
        });

        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', function () {
                this.closest('.item-row').remove();
                calculateTotal();
            });
        });

        document.addEventListener('input', function () {
            calculateTotal();
        });

        function calculateTotal() {
        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('#items-container .item-row').forEach(row => {
                const quantity = parseFloat(row.querySelector('[name$="[quantity]"]').value) || 0;
                const price = parseFloat(row.querySelector('[name$="[unit_price]"]').value) || 0;
                total += quantity * price;
            });
            document.getElementById('total').value = `R$ ${total.toFixed(2).replace('.', ',')}`;
        }
        calculateTotal();
    </script>
@endsection
