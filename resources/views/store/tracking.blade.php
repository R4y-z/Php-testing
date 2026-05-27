@extends('layouts.store')
@section('title', 'Pedido ' . $order->code)

@section('content')
<div class="max-w-lg mx-auto px-4 py-8">
    <div class="text-center mb-6">
        <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-receipt text-3xl text-amber-600"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Pedido Recebido!</h1>
        <p class="text-gray-500 mt-1">Código: <span class="font-mono font-bold text-amber-700 text-lg">{{ $order->code }}</span></p>
    </div>

    {{-- Status --}}
    <div class="card mb-5">
        <h2 class="font-semibold text-gray-800 mb-4 text-center">Status do Pedido</h2>
        @php
            $steps = [
                'recebido'     => ['icon' => 'fa-receipt',     'label' => 'Recebido'],
                'confirmado'   => ['icon' => 'fa-check-circle','label' => 'Confirmado'],
                'preparando'   => ['icon' => 'fa-fire',        'label' => 'Preparando'],
                'pronto'       => ['icon' => 'fa-bell',        'label' => 'Pronto'],
                'saiu_entrega' => ['icon' => 'fa-motorcycle',  'label' => 'Saiu para entrega'],
                'finalizado'   => ['icon' => 'fa-check',       'label' => 'Finalizado'],
            ];
            $statuses = array_keys($steps);
            $currentIndex = array_search($order->status, $statuses);
        @endphp

        <div class="relative">
            <div class="flex justify-between mb-2">
                @foreach($steps as $status => $info)
                @php $index = array_search($status, $statuses); @endphp
                <div class="flex flex-col items-center text-center" style="width: {{ 100/count($steps) }}%">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm
                        {{ $index <= $currentIndex ? 'bg-amber-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                        <i class="fas {{ $info['icon'] }}"></i>
                    </div>
                    <p class="text-xs mt-1 {{ $index <= $currentIndex ? 'text-amber-700 font-semibold' : 'text-gray-400' }} leading-tight">
                        {{ $info['label'] }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>

        <div class="text-center mt-4">
            <span class="badge {{ $order->status_color }} text-sm px-4 py-1.5">{{ $order->status_label }}</span>
        </div>
    </div>

    {{-- Detalhes --}}
    <div class="card mb-5">
        <h2 class="font-semibold text-gray-800 mb-3">Itens do Pedido</h2>
        <div class="space-y-2">
            @foreach($order->items as $item)
            <div class="flex justify-between text-sm">
                <span class="text-gray-700">{{ $item->formatted_quantity }} × {{ $item->product_name }}</span>
                <span class="font-medium text-gray-800">R$ {{ number_format($item->total, 2, ',', '.') }}</span>
            </div>
            @endforeach
        </div>
        <div class="border-t border-gray-100 mt-3 pt-3 flex justify-between font-bold text-gray-900">
            <span>Total</span>
            <span>R$ {{ number_format($order->total, 2, ',', '.') }}</span>
        </div>
    </div>

    <div class="card mb-6 text-sm space-y-2">
        <div class="flex justify-between">
            <span class="text-gray-500">Tipo</span>
            <span class="font-medium capitalize">{{ $order->type }}</span>
        </div>
        @if($order->delivery_address)
        <div class="flex justify-between">
            <span class="text-gray-500">Endereço</span>
            <span class="font-medium text-right max-w-xs">{{ $order->delivery_address }}</span>
        </div>
        @endif
        <div class="flex justify-between">
            <span class="text-gray-500">Pagamento</span>
            <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
        </div>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('store.index') }}" class="btn-secondary flex-1 text-center">
            <i class="fas fa-arrow-left mr-2"></i>Cardápio
        </a>
        <button onclick="location.reload()" class="btn-primary flex-1">
            <i class="fas fa-sync-alt mr-2"></i>Atualizar
        </button>
    </div>

    @if($order->status !== 'finalizado' && $order->status !== 'cancelado')
    <p class="text-center text-xs text-gray-400 mt-4">
        <i class="fas fa-info-circle mr-1"></i>
        Esta página atualiza automaticamente. Guarde o código do pedido.
    </p>
    @push('scripts')
    <script>
        setTimeout(() => location.reload(), 30000);
    </script>
    @endpush
    @endif
</div>
@endsection
