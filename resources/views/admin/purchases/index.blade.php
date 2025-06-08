@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Compras</h1>
            <a href="{{ route('admin.purchases.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                Nova Compra
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
                    <th class="px-4 py-2 border">Fornecedor</th>
                    <th class="px-4 py-2 border">Data</th>
                    <th class="px-4 py-2 border">Parcelas</th>
                    <th class="px-4 py-2 border">Valor Total</th>
                    <th class="px-4 py-2 border">Ações</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($purchases as $purchase)
                    <tr>
                        <td class="px-4 py-2 border">{{ $purchase->id }}</td>
                        <td class="px-4 py-2 border">{{ $purchase->supplier_name ?? '-' }}</td>
                        <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}</td>
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
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
