const incomeData = cashFlowData.map(item => item.income);
const expensesData = cashFlowData.map(item => -item.expenses); // Negativo para mostrar como saída
const balanceData = cashFlowData.map(item => item.@extends('layouts.app')

@section('title', 'Fluxo de Caixa - Dashboards')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center mb-6">
            <a href="{{ route('admin.dashboards.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                ← Voltar aos Dashboards
            </a>
            <h1 class="text-2xl font-bold">Fluxo de Caixa</h1>
        </div>

        <!-- Filtros -->
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <h3 class="text-lg font-semibold mb-3">Filtros de Período</h3>

            <!-- Filtros Rápidos -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Períodos Rápidos:</label>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.dashboards.cash-flow', ['start_date' => now()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}"
                       class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm transition-colors">
                        Hoje
                    </a>
                    <a href="{{ route('admin.dashboards.cash-flow', ['start_date' => now()->subDays(7)->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}"
                       class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm transition-colors">
                        Últimos 7 dias
                    </a>
                    <a href="{{ route('admin.dashboards.cash-flow', ['start_date' => now()->subDays(30)->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}"
                       class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm transition-colors">
                        Últimos 30 dias
                    </a>
                    <a href="{{ route('admin.dashboards.cash-flow', ['start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->endOfMonth()->format('Y-m-d')]) }}"
                       class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm transition-colors">
                        Este mês
                    </a>
                    <a href="{{ route('admin.dashboards.cash-flow', ['start_date' => now()->subMonth()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->subMonth()->endOfMonth()->format('Y-m-d')]) }}"
                       class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm transition-colors">
                        Mês passado
                    </a>
                </div>
            </div>

            <!-- Filtro Manual -->
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Data Inicial</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Data Final</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md transition-colors">
                        🔍 Filtrar
                    </button>
                </div>
                <div>
                    <a href="{{ route('admin.dashboards.cash-flow') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md transition-colors inline-block">
                        🔄 Limpar
                    </a>
                </div>
            </form>
            <div class="mt-3 text-sm text-gray-600">
                <strong>Período selecionado:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} até {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                ({{ \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1 }} dias)
            </div>
        </div>

        <!-- Resumo Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-green-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-green-800 mb-2">Total de Entradas</h3>
                <p class="text-3xl font-bold text-green-600">R$ {{ number_format($totalIncome, 2, ',', '.') }}</p>
                <p class="text-sm text-green-600">{{ $salesCount }} parcelas recebidas</p>
            </div>
            <div class="bg-red-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-red-800 mb-2">Total de Saídas</h3>
                <p class="text-3xl font-bold text-red-600">R$ {{ number_format($totalExpenses, 2, ',', '.') }}</p>
                <p class="text-sm text-red-600">{{ $purchasesCount }} parcelas pagas</p>
            </div>
            <div class="bg-yellow-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-yellow-800 mb-2">Total de Comissões</h3>
                <p class="text-3xl font-bold text-yellow-600">R$ {{ number_format($totalCommission, 2, ',', '.') }}</p>
                <p class="text-sm text-yellow-600">Comissões de vendedores</p>
            </div>
            <div class="bg-blue-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Resultado do Período</h3>
                <p class="text-3xl font-bold {{ ($totalIncome - $totalExpenses) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    R$ {{ number_format($totalIncome - $totalExpenses, 2, ',', '.') }}
                </p>
                <p class="text-sm text-gray-600">Entradas - Saídas</p>
            </div>
        </div>

        <!-- Gráfico -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-xl font-semibold mb-4">Fluxo de Caixa com Comissões - Baseado em Parcelas</h2>
            <div class="mb-4 text-sm text-gray-600">
                <p><strong>Entradas:</strong> Parcelas de vendas efetivamente recebidas</p>
                <p><strong>Saídas:</strong> Parcelas de compras efetivamente pagas</p>
                <p><strong>Comissões:</strong> Valores devidos aos vendedores</p>
            </div>
            <div style="height: 400px;">
                <canvas id="cashFlowChart"></canvas>
            </div>
        </div>

        <!-- Tabela de Dados -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold">Detalhamento por Período</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Período</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entradas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saídas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comissões</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resultado do Dia</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($cashFlowData as $data)
                        @if($data['income'] > 0 || $data['expenses'] > 0 || $data['commission'] > 0)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($data['period'])->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                    @if($data['income'] > 0)
                                        R$ {{ number_format($data['income'], 2, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                    @if($data['expenses'] > 0)
                                        R$ {{ number_format($data['expenses'], 2, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600">
                                    @if($data['commission'] > 0)
                                        R$ {{ number_format($data['commission'], 2, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm {{ ($data['income'] - $data['expenses'] - $data['commission']) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    R$ {{ number_format($data['income'] - $data['expenses'] - $data['commission'], 2, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                Nenhum movimento encontrado para o período selecionado
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('cashFlowChart').getContext('2d');

                const cashFlowData = @json($cashFlowData);
                const labels = cashFlowData.map(item => {
                    const date = new Date(item.period);
                    return date.toLocaleDateString('pt-BR');
                });

                const incomeData = cashFlowData.map(item => item.income);
                const expensesData = cashFlowData.map(item => -item.expenses); // Negativo para mostrar como saída
                const commissionData = cashFlowData.map(item => -item.commission); // Negativo para mostrar como saída

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Entradas (Recebimentos)',
                                data: incomeData,
                                borderColor: 'rgb(34, 197, 94)',
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                fill: false,
                                tension: 0.1,
                                yAxisID: 'y'
                            },
                            {
                                label: 'Saídas (Pagamentos)',
                                data: expensesData,
                                borderColor: 'rgb(239, 68, 68)',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                fill: false,
                                tension: 0.1,
                                yAxisID: 'y'
                            },
                            {
                                label: 'Comissões (Vendedores)',
                                data: commissionData,
                                borderColor: 'rgb(251, 191, 36)',
                                backgroundColor: 'rgba(251, 191, 36, 0.1)',
                                fill: false,
                                tension: 0.1,
                                yAxisID: 'y',
                                borderDash: [5, 5] // Linha tracejada
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Fluxo de Caixa - Entradas vs Saídas vs Comissões'
                            },
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'R$ ' + value.toLocaleString('pt-BR');
                                    }
                                },
                                grid: {
                                    color: function(context) {
                                        if (context.tick.value === 0) {
                                            return 'rgba(0, 0, 0, 0.3)'; // Linha do zero mais escura
                                        }
                                        return 'rgba(0, 0, 0, 0.1)';
                                    }
                                }
                            },
                            x: {
                                ticks: {
                                    maxTicksLimit: 10 // Limitar número de labels no eixo X
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection
