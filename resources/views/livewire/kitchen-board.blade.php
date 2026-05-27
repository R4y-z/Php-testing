<div>
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold text-gray-700">Cozinha ao Vivo</h2>
        <button wire:click="$refresh" class="btn-secondary text-sm">
            <i class="fas fa-sync-alt mr-1"></i>Atualizar
        </button>
    </div>

    @if($orders->isEmpty() && $comandaItems->isEmpty())
    <div class="text-center py-16">
        <i class="fas fa-check-circle text-6xl text-green-200 mb-4 block"></i>
        <p class="text-gray-400 text-xl">Tudo em dia!</p>
    </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($orders as $order)
        <div class="card border-l-4 {{ $order->status === 'confirmado' ? 'border-l-blue-500' : 'border-l-orange-500' }}">
            <div class="flex items-center justify-between mb-2">
                <p class="font-bold font-mono">{{ $order->code }}</p>
                <span class="badge {{ $order->status_color }}">{{ $order->status_label }}</span>
            </div>
            <ul class="text-sm space-y-1 mb-3">
                @foreach($order->items as $item)
                <li>{{ $item->formatted_quantity }} × {{ $item->product_name }}</li>
                @endforeach
            </ul>
            <div class="flex gap-2">
                @if($order->status === 'confirmado')
                <button wire:click="markPreparing({{ $order->id }})" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-2 rounded-lg text-sm">
                    <i class="fas fa-fire mr-1"></i>Preparando
                </button>
                @else
                <button wire:click="markReady({{ $order->id }})" class="flex-1 bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg text-sm">
                    <i class="fas fa-check mr-1"></i>Pronto!
                </button>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
