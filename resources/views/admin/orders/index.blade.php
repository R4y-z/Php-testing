@extends('layouts.admin')
@section('title', 'Pedidos')
@section('page-title', 'Pedidos')

@section('content')
{{-- Contadores de status --}}
<div class="flex flex-wrap gap-2 mb-5">
    @foreach(['recebido' => ['label' => 'Recebidos', 'color' => 'yellow'], 'confirmado' => ['label' => 'Confirmados', 'color' => 'blue'], 'preparando' => ['label' => 'Preparando', 'color' => 'orange'], 'pronto' => ['label' => 'Prontos', 'color' => 'green']] as $status => $info)
    <a href="{{ route('admin.orders.index', ['status' => $status]) }}"
        class="badge bg-{{ $info['color'] }}-100 text-{{ $info['color'] }}-700 text-sm px-3 py-1.5 cursor-pointer hover:bg-{{ $info['color'] }}-200">
        {{ $counts[$status] ?? 0 }} {{ $info['label'] }}
    </a>
    @endforeach
</div>

{{-- Filtros --}}
<div class="card mb-5">
    <form method="GET" class="flex flex-wrap gap-3">
        <select name="status" class="form-input w-40">
            <option value="">Todos status</option>
            @foreach(['recebido','confirmado','preparando','pronto','saiu_entrega','finalizado','cancelado'] as $s)
            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
            @endforeach
        </select>
        <select name="type" class="form-input w-36">
            <option value="">Todos tipos</option>
            @foreach(['delivery','retirada','mesa','balcao'] as $t)
            <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
            @endforeach
        </select>
        <input type="date" name="date" value="{{ request('date', today()->format('Y-m-d')) }}" class="form-input w-40">
        <button type="submit" class="btn-primary"><i class="fas fa-search mr-1"></i>Filtrar</button>
        <a href="{{ route('admin.orders.index') }}" class="btn-secondary">Limpar</a>
    </form>
</div>

<div class="card overflow-hidden p-0">
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Cliente</th>
                <th>Tipo</th>
                <th>Itens</th>
                <th>Total</th>
                <th>Pagamento</th>
                <th>Status</th>
                <th>Hora</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td class="font-mono font-bold text-amber-700">
                    <a href="{{ route('admin.orders.show', $order) }}" class="hover:underline">{{ $order->code }}</a>
                </td>
                <td>{{ $order->customer_name ?? $order->customer?->name ?? '—' }}</td>
                <td><span class="badge bg-gray-100 text-gray-600">{{ ucfirst($order->type) }}</span></td>
                <td>{{ $order->items->count() }} itens</td>
                <td class="font-semibold">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                <td>
                    <span class="badge {{ $order->payment_status === 'pago' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $order->payment_status === 'pago' ? 'Pago' : 'Pendente' }}
                    </span>
                </td>
                <td><span class="badge {{ $order->status_color }}">{{ $order->status_label }}</span></td>
                <td class="text-gray-400 text-xs">{{ $order->created_at->format('H:i') }}</td>
                <td>
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center text-gray-400 py-10">Nenhum pedido encontrado</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $orders->withQueryString()->links() }}</div>
</div>
@endsection
