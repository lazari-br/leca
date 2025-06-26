@extends('layouts.app')

@section('title', 'Painel Administrativo - Leca')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">
            @if(auth()->user()->type->name === 'vendedor')
                Painel do Vendedor
            @else
                Painel Administrativo
            @endif
        </h1>

        @if(auth()->user()->type->name === 'vendedor')
            <!-- Interface para Vendedores -->
            <div class="grid grid-cols-1 gap-6 max-w-md">
                <a href="{{ route('admin.sales.index') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-6 px-6 rounded-lg text-center transition-colors shadow-lg">
                    <div class="text-4xl mb-2">💰</div>
                    <h3 class="text-xl font-semibold">Gerenciar Vendas</h3>
                    <p class="text-green-100 text-sm mt-2">Registre e acompanhe suas vendas</p>
                </a>

                @if(auth()->user()->commission)
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-800 mb-2">Sua Comissão</h3>
                        <p class="text-2xl font-bold text-blue-600">{{ auth()->user()->commission }}%</p>
                        <p class="text-sm text-blue-600">sobre cada venda realizada</p>
                    </div>
                @endif
            </div>
        @else
            <!-- Interface para Administradores -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="{{ route('admin.products.index') }}" class="bg-gray-300 hover:bg-gray-400 text-pink-600 font-bold py-4 px-6 rounded text-center transition-colors">
                    Gerenciar Produtos
                </a>
                <a href="{{ route('admin.purchases.index') }}" class="bg-gray-300 hover:bg-gray-400 text-pink-600 font-bold py-4 px-6 rounded text-center transition-colors">
                    Gerenciar Compras
                </a>
                <a href="{{ route('admin.sales.index') }}" class="bg-gray-300 hover:bg-gray-400 text-pink-600 font-bold py-4 px-6 rounded text-center transition-colors">
                    Gerenciar Vendas
                </a>
                <a href="{{ route('admin.sellers.index') }}" class="bg-gray-300 hover:bg-gray-400 text-pink-600 font-bold py-4 px-6 rounded text-center transition-colors">
                    Gerenciar Vendedores
                </a>
                <a href="{{ route('admin.dashboards.index') }}" class="bg-gray-300 hover:bg-gray-400 text-pink-600 font-bold py-4 px-6 rounded text-center transition-colors">
                    Dashboards
                </a>
            </div>
        @endif
    </div>
@endsection
