@extends('layouts.store')
@section('title', 'Carrinho')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">
        <i class="fas fa-shopping-cart mr-2 text-amber-600"></i>Seu Carrinho
    </h1>

    @if(empty($cart))
    <div class="text-center py-16">
        <i class="fas fa-shopping-cart text-6xl text-gray-200 mb-4 block"></i>
        <p class="text-gray-400 text-xl mb-6">Carrinho vazio</p>
        <a href="{{ route('store.index') }}" class="btn-primary">
            <i class="fas fa-arrow-left mr-2"></i>Ver Cardápio
        </a>
    </div>
    @else
    <div class="card overflow-hidden p-0 mb-4">
        @foreach($cart as $key => $item)
        <div class="flex items-center gap-4 px-5 py-4 border-b border-gray-100 last:border-0" id="item-{{ $key }}">
            <div class="w-14 h-14 bg-amber-50 rounded-xl flex items-center justify-center flex-shrink-0">
                @if($item['image'] && !str_contains($item['image'], 'default'))
                <img src="{{ $item['image'] }}" class="w-14 h-14 rounded-xl object-cover">
                @else
                <i class="fas fa-utensils text-amber-400"></i>
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-800">{{ $item['name'] }}</p>
                <p class="text-amber-700 font-bold">R$ {{ number_format($item['price'], 2, ',', '.') }}{{ $item['unit'] === 'kg' ? '/kg' : '' }}</p>
            </div>

            <div class="flex items-center gap-2">
                @if($item['unit'] === 'un')
                <button onclick="updateQty('{{ $key }}', {{ $item['quantity'] - 1 }})"
                    class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600">
                    <i class="fas fa-minus text-xs"></i>
                </button>
                <span class="w-8 text-center font-bold text-gray-800">{{ intval($item['quantity']) }}</span>
                <button onclick="updateQty('{{ $key }}', {{ $item['quantity'] + 1 }})"
                    class="w-8 h-8 rounded-full bg-amber-100 hover:bg-amber-200 flex items-center justify-center text-amber-700">
                    <i class="fas fa-plus text-xs"></i>
                </button>
                @else
                <span class="font-medium text-gray-700">{{ number_format($item['quantity'], 3, ',', '.') }} kg</span>
                @endif
            </div>

            <div class="text-right">
                <p class="font-bold text-gray-900">R$ {{ number_format($item['total'], 2, ',', '.') }}</p>
                <button onclick="removeItem('{{ $key }}')" class="text-red-400 hover:text-red-600 text-xs mt-1">
                    <i class="fas fa-trash mr-1"></i>Remover
                </button>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Resumo --}}
    <div class="card mb-6">
        @php
            $subtotal = array_sum(array_column($cart, 'total'));
            $deliveryFee = (float)($delivery['delivery_fee'] ?? 5);
        @endphp
        <div class="space-y-2">
            <div class="flex justify-between text-sm text-gray-600">
                <span>Subtotal ({{ count($cart) }} {{ count($cart) === 1 ? 'item' : 'itens' }})</span>
                <span>R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-sm text-gray-500">
                <span>Taxa de entrega (delivery)</span>
                <span>R$ {{ number_format($deliveryFee, 2, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('store.index') }}" class="btn-secondary flex-shrink-0">
            <i class="fas fa-arrow-left mr-2"></i>Continuar
        </a>
        <a href="{{ route('store.checkout') }}" class="btn-primary flex-1 text-center">
            <i class="fas fa-check mr-2"></i>Finalizar Pedido
        </a>
    </div>
    @endif
</div>

@push('scripts')
<script>
function updateQty(key, newQty) {
    fetch('/loja/carrinho/update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ product_id: key.replace('product_', ''), quantity: newQty })
    }).then(() => location.reload());
}

function removeItem(key) {
    fetch('/loja/carrinho/remove', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ product_id: key.replace('product_', '') })
    }).then(() => location.reload());
}
</script>
@endpush
@endsection
