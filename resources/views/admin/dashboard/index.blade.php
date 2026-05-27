@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- Cards de resumo --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-shopping-bag text-amber-600 text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-xs">Pedidos Hoje</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['orders_today'] }}</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-xs">Faturamento Hoje</p>
                <p class="text-xl font-bold text-gray-800">R$ {{ number_format($stats['revenue_today'], 2, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-clock text-orange-600 text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-xs">Em Andamento</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['pending_orders'] }}</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-chair text-blue-600 text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-xs">Mesas Ocupadas</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['open_tables'] }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Segunda linha de cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-receipt text-yellow-600"></i>
            </div>
            <div>
                <p class="text-gray-500 text-xs">Comandas Abertas</p>
                <p class="text-xl font-bold text-gray-800">{{ $stats['open_comandas'] }}</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-chair text-green-600"></i>
            </div>
            <div>
                <p class="text-gray-500 text-xs">Mesas Disponíveis</p>
                <p class="text-xl font-bold text-gray-800">{{ $stats['available_tables'] }}</p>
            </div>
        </div>
    </div>

    <div class="card {{ $stats['low_stock'] > 0 ? 'border-red-200' : '' }}">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 {{ $stats['low_stock'] > 0 ? 'bg-red-100' : 'bg-gray-100' }} rounded-xl flex items-center justify-center">
                <i class="fas fa-boxes-stacked {{ $stats['low_stock'] > 0 ? 'text-red-600' : 'text-gray-500' }}"></i>
            </div>
            <div>
                <p class="text-gray-500 text-xs">Estoque Baixo</p>
                <p class="text-xl font-bold {{ $stats['low_stock'] > 0 ? 'text-red-600' : 'text-gray-800' }}">{{ $stats['low_stock'] }}</p>
            </div>
        </div>
    </div>

    <div class="card {{ $stats['cash_session'] ? 'border-green-200' : 'border-red-200' }}">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 {{ $stats['cash_session'] ? 'bg-green-100' : 'bg-red-100' }} rounded-xl flex items-center justify-center">
                <i class="fas fa-cash-register {{ $stats['cash_session'] ? 'text-green-600' : 'text-red-500' }}"></i>
            </div>
            <div>
                <p class="text-gray-500 text-xs">Caixa</p>
                <p class="text-sm font-bold {{ $stats['cash_session'] ? 'text-green-600' : 'text-red-500' }}">
                    {{ $stats['cash_session'] ? 'Aberto' : 'Fechado' }}
                </p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Pedidos Recentes --}}
    <div class="lg:col-span-2 card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800">Pedidos Recentes</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-brand-600 text-sm hover:underline">Ver todos</a>
        </div>
        <div class="overflow-x-auto">
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_orders as $order)
                    <tr>
                        <td><a href="{{ route('admin.orders.show', $order) }}" class="font-mono text-brand-600 hover:underline">{{ $order->code }}</a></td>
                        <td>{{ $order->customer_name ?? $order->customer?->name ?? '—' }}</td>
                        <td class="capitalize">{{ $order->type }}</td>
                        <td>R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                        <td><span class="badge {{ $order->status_color }}">{{ $order->status_label }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-gray-400 py-8">Nenhum pedido hoje</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Produtos mais vendidos --}}
    <div class="card">
        <h3 class="font-semibold text-gray-800 mb-4">Top Produtos</h3>
        @forelse($top_products as $i => $product)
        <div class="flex items-center gap-3 py-2 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
            <span class="w-6 h-6 rounded-full bg-brand-100 text-brand-700 text-xs flex items-center justify-center font-bold">{{ $i+1 }}</span>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-800 truncate">{{ $product->name }}</p>
                <p class="text-xs text-gray-400">{{ $product->order_items_count }} pedidos</p>
            </div>
            <span class="text-xs text-gray-500">{{ $product->formatted_price }}</span>
        </div>
        @empty
        <p class="text-gray-400 text-sm text-center py-4">Sem dados</p>
        @endforelse
    </div>
</div>

{{-- Atalhos rápidos --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6">
    @if(in_array(auth()->user()->role?->slug, ['admin','garcom','caixa']))
    <a href="{{ route('admin.comandas.index') }}"
        class="bg-amber-50 border border-amber-200 hover:bg-amber-100 rounded-xl p-4 text-center transition-colors group">
        <i class="fas fa-receipt text-2xl text-amber-600 mb-2 block"></i>
        <span class="text-sm font-medium text-gray-700">Nova Comanda</span>
    </a>
    @endif

    @if(in_array(auth()->user()->role?->slug, ['admin','garcom']))
    <a href="{{ route('admin.orders.index') }}"
        class="bg-blue-50 border border-blue-200 hover:bg-blue-100 rounded-xl p-4 text-center transition-colors">
        <i class="fas fa-shopping-bag text-2xl text-blue-600 mb-2 block"></i>
        <span class="text-sm font-medium text-gray-700">Ver Pedidos</span>
    </a>
    @endif

    @if(in_array(auth()->user()->role?->slug, ['admin','cozinha']))
    <a href="{{ route('admin.kitchen.index') }}"
        class="bg-orange-50 border border-orange-200 hover:bg-orange-100 rounded-xl p-4 text-center transition-colors">
        <i class="fas fa-kitchen-set text-2xl text-orange-600 mb-2 block"></i>
        <span class="text-sm font-medium text-gray-700">Cozinha</span>
    </a>
    @endif

    @if(in_array(auth()->user()->role?->slug, ['admin','caixa']))
    <a href="{{ route('admin.cash.index') }}"
        class="bg-green-50 border border-green-200 hover:bg-green-100 rounded-xl p-4 text-center transition-colors">
        <i class="fas fa-cash-register text-2xl text-green-600 mb-2 block"></i>
        <span class="text-sm font-medium text-gray-700">Caixa</span>
    </a>
    @endif
</div>

@endsection
