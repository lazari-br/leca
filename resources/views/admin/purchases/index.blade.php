@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Compras</h1>
            <div class="flex gap-2">
                <!-- Dropdown de Exportação -->
                <div class="relative inline-block text-left">
                    <button type="button" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded inline-flex items-center" id="export-menu-button" aria-expanded="true" aria-haspopup="true" onclick="toggleExportMenu()">
                        📊 Exportar CSV
                        <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden" role="menu" aria-orientation="vertical" aria-labelledby="export-menu-button" id="export-menu">
                        <div class="py-1" role="none">
                            <a href="{{ route('admin.purchases.export.total') }}" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100" role="menuitem">
                                Relatório Total de Compras
                                <span class="text-xs text-gray-500 block">Data, SKU, produto, quantidade, fornecedor, valor total, parcelas</span>
                            </a>
                            <a href="{{ route('admin.purchases.export.monthly') }}" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100" role="menuitem">
                                Relatório Mensal de Compras
                                <span class="text-xs text-gray-500 block">Data, SKU, produto, quantidade, fornecedor, valor da parcela</span>
                            </a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('admin.purchases.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Nova Compra
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filtros -->
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Data Inicial</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="mt-1 block w-full border-gray-300 rounded-md">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">Data Final</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="mt-1 block w-full border-gray-300 rounded-md">
                </div>
                <div>
                    <label for="supplier" class="block text-sm font-medium text-gray-700">Fornecedor</label>
                    <input type="text" name="supplier" id="supplier" value="{{ request('supplier') }}" placeholder="Nome do fornecedor" class="mt-1 block w-full border-gray-300 rounded-md">
                </div>
                <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                    <input type="text" name="sku" id="sku" value="{{ request('sku') }}" placeholder="Código do SKU" class="mt-1 block w-full border-gray-300 rounded-md">
                </div>
                <div>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                        🔍 Filtrar
                    </button>
                </div>
                <div>
                    <a href="{{ route('admin.purchases.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                        🔄 Limpar
                    </a>
                </div>
            </form>
        </div>

        <!-- Resumo -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-800">Total de Compras</h3>
                <p class="text-2xl font-bold text-blue-600">{{ $purchases->count() }}</p>
            </div>
            <div class="bg-red-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-red-800">Valor Total</h3>
                <p class="text-2xl font-bold text-red-600">R$ {{ number_format($purchases->sum('total'), 2, ',', '.') }}</p>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-yellow-800">Média por Compra</h3>
                <p class="text-2xl font-bold text-yellow-600">
                    R$ {{ $purchases->count() > 0 ? number_format($purchases->sum('total') / $purchases->count(), 2, ',', '.') : '0,00' }}
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="px-4 py-2 border">ID</th>
                    <th class="px-4 py-2 border">Fornecedor</th>
                    <th class="px-4 py-2 border">Data</th>
                    <th class="px-4 py-2 border">SKUs Comprados</th>
                    <th class="px-4 py-2 border">Método Pagamento</th>
                    <th class="px-4 py-2 border">Parcelas</th>
                    <th class="px-4 py-2 border">Valor Total</th>
                    <th class="px-4 py-2 border">Ações</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($purchases as $purchase)
                    <tr>
                        <td class="px-4 py-2 border">{{ $purchase->id }}</td>
                        <td class="px-4 py-2 border">{{ $purchase->supplier_name ?? '-' }}</td>
                        <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}</td>
                        <td class="px-4 py-2 border">
                            @if($purchase->items && $purchase->items->count() > 0)
                                <div class="space-y-1">
                                    @foreach($purchase->items->take(3) as $item)
                                        @if($item->variation)
                                            @php
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
                                                if ($item->variation->color) {
                                                    $colorDisplay = $colorMap[$item->variation->color] ?? $item->variation->color;
                                                }
                                            @endphp
                                            <div class="text-xs bg-gray-100 px-2 py-1 rounded">
                                                <strong>{{ $item->variation->code }}</strong> - {{ $item->variation->product->name ?? 'Produto não encontrado' }}
                                                <br>
                                                <span class="text-gray-600">{{ $item->variation->size }}{{ $colorDisplay ? ' - ' . $colorDisplay : '' }} (Qtd: {{ $item->quantity }})</span>
                                            </div>
                                        @else
                                            <div class="text-xs bg-red-100 px-2 py-1 rounded text-red-600">
                                                SKU não encontrado (Item #{{ $item->id }})
                                            </div>
                                        @endif
                                    @endforeach
                                    @if($purchase->items->count() > 3)
                                        <div class="text-xs text-gray-500">
                                            +{{ $purchase->items->count() - 3 }} SKUs mais...
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-500 text-xs">Nenhum item</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 border">{{ $purchase->payment_method ?? '-' }}</td>
                        <td class="px-4 py-2 border">{{ $purchase->installments ?? '-' }}</td>
                        <td class="px-4 py-2 border">R$ {{ number_format($purchase->total, 2, ',', '.') }}</td>
                        <td class="px-4 py-2 border">
                            <a href="{{ route('admin.purchases.edit', $purchase->id) }}" class="text-blue-500 hover:underline">Editar</a>
                            <form action="{{ route('admin.purchases.destroy', $purchase->id) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Deseja realmente excluir esta compra?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-4 text-center text-gray-500">
                            Nenhuma compra encontrada
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($purchases, 'links'))
            <div class="mt-4">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>

    <script>
        function toggleExportMenu() {
            const menu = document.getElementById('export-menu');
            menu.classList.toggle('hidden');
        }

        // Fechar menu ao clicar fora
        window.addEventListener('click', function(e) {
            const button = document.getElementById('export-menu-button');
            const menu = document.getElementById('export-menu');
            if (!button.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>
@endsection
