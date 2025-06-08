@extends('layouts.app')

@section('title', 'Dashboards - Leca')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center mb-6">
            <a href="{{ route('admin.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                ← Voltar ao Painel
            </a>
            <h1 class="text-2xl font-bold">Dashboards</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('admin.dashboards.cash-flow') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-8 px-6 rounded-lg text-center transform hover:scale-105 transition-transform">
                <div class="text-4xl mb-4">📊</div>
                <h3 class="text-xl font-semibold mb-2">Fluxo de Caixa</h3>
                <p class="text-blue-100">Visualize entradas e saídas ao longo do tempo</p>
            </a>

            <a href="{{ route('admin.dashboards.sales-report') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-8 px-6 rounded-lg text-center transform hover:scale-105 transition-transform">
                <div class="text-4xl mb-4">📈</div>
                <h3 class="text-xl font-semibold mb-2">Relatório de Vendas</h3>
                <p class="text-green-100">Análise detalhada das vendas realizadas</p>
            </a>

            <a href="{{ route('admin.dashboards.purchases-report') }}" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-8 px-6 rounded-lg text-center transform hover:scale-105 transition-transform">
                <div class="text-4xl mb-4">📉</div>
                <h3 class="text-xl font-semibold mb-2">Relatório de Compras</h3>
                <p class="text-orange-100">Análise detalhada das compras realizadas</p>
            </a>
        </div>

        <div class="mt-8 bg-gray-50 p-6 rounded-lg">
            <h2 class="text-lg font-semibold mb-4">Resumo Rápido</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded shadow">
                    <h3 class="text-sm font-medium text-gray-500">Total Vendas (Mês)</h3>
                    <p class="text-2xl font-bold text-green-600">R$ {{ number_format($monthlySales, 2, ',', '.') }}</p>
                </div>
                <div class="bg-white p-4 rounded shadow">
                    <h3 class="text-sm font-medium text-gray-500">Total Compras (Mês)</h3>
                    <p class="text-2xl font-bold text-red-600">R$ {{ number_format($monthlyPurchases, 2, ',', '.') }}</p>
                </div>
                <div class="bg-white p-4 rounded shadow">
                    <h3 class="text-sm font-medium text-gray-500">Saldo do Mês</h3>
                    <p class="text-2xl font-bold {{ ($monthlySales - $monthlyPurchases) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        R$ {{ number_format($monthlySales - $monthlyPurchases, 2, ',', '.') }}
                    </p>
                </div>
                <div class="bg-white p-4 rounded shadow">
                    <h3 class="text-sm font-medium text-gray-500">Parcelas Pendentes</h3>
                    <p class="text-2xl font-bold text-yellow-600">{{ $pendingInstallments }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
