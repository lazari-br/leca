@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Vendas</h1>
            <a href="{{ route('admin.sales.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                Nova Venda
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="px-4 py-2 border">ID</th>
                    <th class="px-4 py-2 border">Cliente</th>
                    <th class="px-4 py-2 border">Vendedor</th>
                    <th class="px-4 py-2 border">Data</th>
                    <th class="px-4 py-2 border">Parcelas</th>
                    <th class="px-4 py-2 border">Valor Total</th>
                    <th class="px-4 py-2 border">Ações</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($sales as $sale)
                    <tr>
                        <td class="px-4 py-2 border">{{ $sale->id }}</td>
                        <td class="px-4 py-2 border">{{ $sale->customer_name ?? '-' }}</td>
                        <td class="px-4 py-2 border">{{ $sale->seller?->name ?? '-' }}</td>
                        <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</td>
                        <td class="px-4 py-2 border">{{ $sale->installments ?? '-' }}</td>
                        <td class="px-4 py-2 border">R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
                        <td class="px-4 py-2 border">
                            <a href="{{ route('admin.sales.edit', $sale->id) }}" class="text-blue-500 hover:underline">Editar</a>
                            <form action="{{ route('admin.sales.destroy', $sale->id) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Deseja realmente excluir esta venda?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
