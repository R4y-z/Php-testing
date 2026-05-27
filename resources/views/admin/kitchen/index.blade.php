@extends('layouts.admin')
@section('title', 'Cozinha')
@section('page-title', 'Tela da Cozinha')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="flex gap-2">
        <span class="badge bg-blue-100 text-blue-700 text-sm px-3 py-1.5">{{ $orders->where('status','confirmado')->count() }} novos</span>
        <span class="badge bg-orange-100 text-orange-700 text-sm px-3 py-1.5">{{ $orders->where('status','preparando')->count() }} preparando</span>
    </div>
    <button onclick="location.reload()" class="btn-secondary text-sm">
        <i class="fas fa-sync-alt mr-1"></i>Atualizar
    </button>
</div>

{{-- Pedidos Online --}}
@if($orders->isNotEmpty())
<h2 class="font-semibold text-gray-700 mb-3 text-sm uppercase tracking-wide">
    <i class="fas fa-shopping-bag mr-1 text-gray-400"></i> Pedidos
</h2>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
    @foreach($orders as $order)
    <div class="card border-l-4 {{ $order->status === 'confirmado' ? 'border-l-blue-500' : 'border-l-orange-500' }}">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="font-bold font-mono text-lg">{{ $order->code }}</p>
                @if($order->table)
                <p class="text-xs text-gray-400"><i class="fas fa-chair mr-1"></i>{{ $order->table->display_name }}</p>
                @else
                <p class="text-xs text-gray-400">{{ ucfirst($order->type) }}</p>
                @endif
            </div>
            <span class="badge {{ $order->status_color }}">{{ $order->status_label }}</span>
        </div>

        <ul class="space-y-1 mb-3">
            @foreach($order->items as $item)
            <li class="flex justify-between text-sm">
                <span class="text-gray-700"><span class="font-bold text-gray-900">{{ $item->formatted_quantity }}</span> — {{ $item->product_name }}</span>
                @if($item->notes)
                <span class="text-xs text-amber-600 ml-1">({{ $item->notes }})</span>
                @endif
            </li>
            @endforeach
        </ul>

        <p class="text-xs text-gray-400 mb-3">Há {{ $order->created_at->diffForHumans() }}</p>

        <div class="flex gap-2">
            @if($order->status === 'confirmado')
            <form action="{{ route('admin.kitchen.order-status', $order) }}" method="POST" class="flex-1">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="preparando">
                <button class="w-full bg-orange-500 hover:bg-orange-600 text-white py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-fire mr-1"></i>Preparando
                </button>
            </form>
            @elseif($order->status === 'preparando')
            <form action="{{ route('admin.kitchen.order-status', $order) }}" method="POST" class="flex-1">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="pronto">
                <button class="w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-check mr-1"></i>Pronto!
                </button>
            </form>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Itens de Comanda --}}
@if($comandaItems->isNotEmpty())
<h2 class="font-semibold text-gray-700 mb-3 text-sm uppercase tracking-wide">
    <i class="fas fa-receipt mr-1 text-gray-400"></i> Itens de Comandas
</h2>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($comandaItems as $item)
    <div class="card border-l-4 border-l-yellow-400">
        <div class="flex items-center justify-between mb-2">
            <p class="font-bold">Comanda {{ $item->comanda->number }}</p>
            @if($item->comanda->table)
            <span class="text-xs text-gray-400"><i class="fas fa-chair mr-1"></i>{{ $item->comanda->table->display_name }}</span>
            @endif
        </div>
        <p class="text-lg font-semibold text-gray-800">
            {{ $item->formatted_quantity }} × {{ $item->product_name }}
        </p>
        @if($item->notes)
        <p class="text-xs text-amber-600 mt-1"><i class="fas fa-comment mr-1"></i>{{ $item->notes }}</p>
        @endif
        <p class="text-xs text-gray-400 mt-1">Há {{ $item->created_at->diffForHumans() }}</p>
        <form action="{{ route('admin.kitchen.item-status', $item) }}" method="POST" class="mt-2">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="pronto">
            <button class="w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-check mr-1"></i>Marcar como pronto
            </button>
        </form>
    </div>
    @endforeach
</div>
@endif

@if($orders->isEmpty() && $comandaItems->isEmpty())
<div class="text-center py-20">
    <i class="fas fa-check-circle text-6xl text-green-200 mb-4 block"></i>
    <p class="text-gray-400 text-xl">Cozinha em dia! Nenhum pedido pendente.</p>
</div>
@endif

@push('scripts')
<script>
    // Auto-refresh a cada 30 segundos
    setTimeout(() => location.reload(), 30000);
</script>
@endpush
@endsection
