@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Vendedores</h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.sellers.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Novo Vendedor
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Resumo -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-800">Total de Vendedores</h3>
                <p class="text-2xl font-bold text-blue-600">{{ $sellers->count() }}</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-green-800">Produtos Vendidos (30 dias)</h3>
                <p class="text-2xl font-bold text-green-600">{{ $sellers->sum('products_sold_30_days') }}</p>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-purple-800">Vendas Totais (30 dias)</h3>
                <p class="text-2xl font-bold text-purple-600">R$ {{ number_format($sellers->sum('sales_value_30_days'), 2, ',', '.') }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="px-4 py-2 border">ID</th>
                    <th class="px-4 py-2 border">Nome</th>
                    <th class="px-4 py-2 border">Email</th>
                    <th class="px-4 py-2 border">Comissão (%)</th>
                    <th class="px-4 py-2 border">Produtos Vendidos (30 dias)</th>
                    <th class="px-4 py-2 border">Valor Total Vendas (30 dias)</th>
                    <th class="px-4 py-2 border">Data Cadastro</th>
                    <th class="px-4 py-2 border">Ações</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($sellers as $seller)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border">{{ $seller->id }}</td>
                        <td class="px-4 py-2 border">
                            <div class="font-medium text-gray-900">{{ $seller->name }}</div>
                        </td>
                        <td class="px-4 py-2 border">{{ $seller->email }}</td>
                        <td class="px-4 py-2 border">
                            @if($seller->commission)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ number_format($seller->commission, 1) }}%
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 border text-center">
                            @if($seller->products_sold_30_days > 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $seller->products_sold_30_days }} unidades
                                </span>
                            @else
                                <span class="text-gray-400">0 unidades</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 border text-center">
                            @if($seller->sales_value_30_days > 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    R$ {{ number_format($seller->sales_value_30_days, 2, ',', '.') }}
                                </span>
                            @else
                                <span class="text-gray-400">R$ 0,00</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 border">
                            {{ $seller->created_at ? $seller->created_at->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-4 py-2 border">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.sellers.edit', $seller->id) }}"
                                   class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                                    Editar
                                </a>
                                <form action="{{ route('admin.sellers.destroy', $seller->id) }}"
                                      method="POST"
                                      class="inline-block"
                                      onsubmit="return confirm('Tem certeza que deseja excluir este vendedor? Esta ação não pode ser desfeita.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-500 hover:text-red-700 text-sm font-medium">
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <p class="text-lg font-medium">Nenhum vendedor cadastrado</p>
                                <p class="text-sm">Clique em "Novo Vendedor" para começar</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
