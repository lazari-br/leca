@extends('layouts.app')

@section('title', 'Painel Administrativo - Leca')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">Painel Administrativo</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="{{ route('admin.products.index') }}" class="bg-gray-300 hover:bg-gray-300 text-pink-600 font-bold py-4 px-6 rounded text-center">
                Gerenciar Produtos
            </a>
            <a href="{{ route('admin.purchases.index') }}" class="bg-gray-300 hover:bg-gray-300 text-pink-600 font-bold py-4 px-6 rounded text-center">
                Gerenciar Compras
            </a>
            <a href="{{ route('admin.sales.index') }}" class="bg-gray-300 hover:bg-gray-300 text-pink-600 font-bold py-4 px-6 rounded text-center">
                Gerenciar Vendas
            </a>
            <a href="#" class="bg-gray-300 hover:bg-gray-300 text-pink-600 font-bold py-4 px-6 rounded text-center">
                Dashboards (em breve)
            </a>
        </div>
    </div>
@endsection
