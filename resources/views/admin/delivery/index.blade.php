@extends('layouts.admin')
@section('title', 'Delivery')
@section('page-title', 'Delivery')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Pedidos ativos --}}
    <div>
        <h2 class="font-semibold text-gray-700 mb-3 text-sm uppercase tracking-wide">
            <i class="fas fa-motorcycle mr-1 text-gray-400"></i> Em andamento
        </h2>
        <div class="space-y-3">
            @forelse($orders as $order)
            <div class="card border-l-4 {{ $order->status === 'pronto' ? 'border-l-green-500' : ($order->status === 'saiu_entrega' ? 'border-l-blue-500' : 'border-l-orange-400') }}">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="font-bold font-mono text-amber-700 text-lg">{{ $order->code }}</p>
                        <p class="font-medium text-gray-800">{{ $order->customer_name ?? $order->customer?->name }}</p>
                        <p class="text-sm text-gray-500">{{ $order->customer_phone }}</p>
                    </div>
                    <span class="badge {{ $order->status_color }}">{{ $order->status_label }}</span>
                </div>

                @if($order->delivery_address)
                <p class="text-sm text-gray-600 mb-2">
                    <i class="fas fa-map-marker-alt mr-1 text-red-400"></i>{{ $order->delivery_address }}
                </p>
                @endif

                <div class="text-sm text-gray-500 mb-3">
                    {{ $order->items->count() }} itens — <span class="font-bold text-gray-800">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                    — {{ $order->created_at->diffForHumans() }}
                </div>

                <div class="flex gap-2">
                    @if($order->status === 'pronto')
                    <form action="{{ route('admin.delivery.status', $order) }}" method="POST" class="flex-1">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="saiu_entrega">
                        <button class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg text-sm font-medium">
                            <i class="fas fa-motorcycle mr-1"></i>Saiu para entrega
                        </button>
                    </form>
                    @elseif($order->status === 'saiu_entrega')
                    <form action="{{ route('admin.delivery.status', $order) }}" method="POST" class="flex-1">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="finalizado">
                        <button class="w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg text-sm font-medium">
                            <i class="fas fa-check mr-1"></i>Entregue
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('admin.orders.show', $order) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="card text-center py-10">
                <i class="fas fa-motorcycle text-5xl text-gray-200 mb-3 block"></i>
                <p class="text-gray-400">Nenhum pedido de delivery ativo</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Histórico hoje --}}
    <div>
        <h2 class="font-semibold text-gray-700 mb-3 text-sm uppercase tracking-wide">
            <i class="fas fa-history mr-1 text-gray-400"></i> Finalizados Hoje
        </h2>
        <div class="card overflow-hidden p-0">
            @forelse($history as $order)
            <div class="px-5 py-3 border-b border-gray-100 last:border-0 flex items-center justify-between">
                <div>
                    <p class="font-mono font-bold text-gray-700 text-sm">{{ $order->code }}</p>
                    <p class="text-xs text-gray-500">{{ $order->customer_name ?? $order->customer?->name }}</p>
                </div>
                <div class="text-right">
                    <p class="font-semibold text-gray-800">R$ {{ number_format($order->total, 2, ',', '.') }}</p>
                    <span class="badge {{ $order->status_color }} text-xs">{{ $order->status_label }}</span>
                </div>
            </div>
            @empty
            <div class="py-8 text-center text-gray-400 text-sm">Nenhuma entrega hoje</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
