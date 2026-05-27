@extends('layouts.admin')
@section('title', 'Pedido ' . $order->code)
@section('page-title', 'Pedido ' . $order->code)

@section('content')
<div class="max-w-3xl">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        {{-- Informações --}}
        <div class="card">
            <h3 class="font-semibold text-gray-800 mb-3">Informações</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Código</span><span class="font-mono font-bold">{{ $order->code }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Cliente</span><span>{{ $order->customer_name ?? $order->customer?->name ?? '—' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Telefone</span><span>{{ $order->customer_phone ?? '—' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Tipo</span><span class="capitalize">{{ $order->type }}</span></div>
                @if($order->delivery_address)
                <div class="flex justify-between"><span class="text-gray-500">Endereço</span><span class="text-right">{{ $order->delivery_address }}</span></div>
                @endif
                @if($order->table)
                <div class="flex justify-between"><span class="text-gray-500">Mesa</span><span>{{ $order->table->display_name }}</span></div>
                @endif
                @if($order->notes)
                <div class="flex justify-between"><span class="text-gray-500">Obs</span><span>{{ $order->notes }}</span></div>
                @endif
                <div class="flex justify-between"><span class="text-gray-500">Criado em</span><span>{{ $order->created_at->format('d/m/Y H:i') }}</span></div>
            </div>
        </div>

        {{-- Pagamento e status --}}
        <div class="card">
            <h3 class="font-semibold text-gray-800 mb-3">Pagamento</h3>
            <div class="space-y-2 text-sm mb-4">
                <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span>R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span></div>
                @if($order->delivery_fee > 0)
                <div class="flex justify-between"><span class="text-gray-500">Entrega</span><span>R$ {{ number_format($order->delivery_fee, 2, ',', '.') }}</span></div>
                @endif
                <div class="flex justify-between font-bold text-gray-900 border-t pt-2"><span>Total</span><span>R$ {{ number_format($order->total, 2, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Forma</span><span>{{ ucfirst(str_replace('_',' ',$order->payment_method)) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Situação</span>
                    <span class="badge {{ $order->payment_status === 'pago' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $order->payment_status === 'pago' ? 'Pago' : 'Pendente' }}
                    </span>
                </div>
            </div>

            {{-- Atualizar status --}}
            <h3 class="font-semibold text-gray-800 mb-2">Status do Pedido</h3>
            <div class="mb-3"><span class="badge {{ $order->status_color }} text-sm">{{ $order->status_label }}</span></div>
            @if(!in_array($order->status, ['finalizado','cancelado']))
            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="flex gap-2">
                @csrf @method('PATCH')
                <select name="status" class="form-input flex-1">
                    @foreach(['recebido','confirmado','preparando','pronto','saiu_entrega','finalizado','cancelado'] as $s)
                    <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-primary flex-shrink-0">Salvar</button>
            </form>
            @endif
        </div>
    </div>

    {{-- Itens --}}
    <div class="card overflow-hidden p-0">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h3 class="font-semibold text-gray-800">Itens do Pedido</h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Qtd</th>
                    <th>Unitário</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <p class="font-medium">{{ $item->product_name }}</p>
                        @if($item->notes)
                        <p class="text-xs text-gray-400">{{ $item->notes }}</p>
                        @endif
                    </td>
                    <td>{{ $item->formatted_quantity }}</td>
                    <td>R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td class="font-semibold">R$ {{ number_format($item->total, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex gap-3">
        <a href="{{ route('admin.orders.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Voltar
        </a>
        @if($order->canBeCancelled())
        <form action="{{ route('admin.orders.cancel', $order) }}" method="POST"
            onsubmit="return confirm('Cancelar este pedido?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-danger"><i class="fas fa-times mr-2"></i>Cancelar Pedido</button>
        </form>
        @endif
    </div>
</div>
@endsection
