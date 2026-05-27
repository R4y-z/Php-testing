<div>
    @if($showCart)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-end" wire:click.self="$set('showCart', false)">
        <div class="bg-white w-full max-w-sm h-full overflow-y-auto">
            <div class="p-5 border-b flex items-center justify-between">
                <h2 class="font-bold text-lg">Carrinho ({{ $this->count }})</h2>
                <button wire:click="$set('showCart', false)" class="text-gray-400">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            @if(empty($cart))
            <div class="p-8 text-center">
                <i class="fas fa-shopping-cart text-4xl text-gray-200 mb-3 block"></i>
                <p class="text-gray-400">Carrinho vazio</p>
            </div>
            @else
            <div class="p-4 space-y-3 flex-1">
                @foreach($cart as $key => $item)
                <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">{{ $item['name'] }}</p>
                        <p class="text-xs text-amber-700">R$ {{ number_format($item['price'], 2, ',', '.') }}/{{ $item['unit'] }}</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <button wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] - 1 }})"
                            class="w-7 h-7 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs flex items-center justify-center">
                            <i class="fas fa-minus"></i>
                        </button>
                        <span class="w-8 text-center text-sm font-bold">{{ intval($item['quantity']) }}</span>
                        <button wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] + 1 }})"
                            class="w-7 h-7 rounded-full bg-amber-100 hover:bg-amber-200 text-amber-700 text-xs flex items-center justify-center">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <p class="text-sm font-bold w-16 text-right">R$ {{ number_format($item['total'], 2, ',', '.') }}</p>
                </div>
                @endforeach
            </div>

            <div class="p-4 border-t">
                <div class="flex justify-between font-bold text-lg mb-4">
                    <span>Total</span>
                    <span>R$ {{ number_format($this->subtotal, 2, ',', '.') }}</span>
                </div>
                <a href="{{ route('store.checkout') }}" class="btn-primary w-full block text-center py-3">
                    <i class="fas fa-check mr-2"></i>Finalizar Pedido
                </a>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
