@extends('layouts.admin')
@section('title', 'Cliente: ' . $customer->name)
@section('page-title', $customer->name)

@section('content')
<div class="max-w-3xl">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="card">
            <h3 class="font-semibold text-gray-800 mb-3">Dados do Cliente</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Nome</span><span class="font-medium">{{ $customer->name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Telefone</span><span>{{ $customer->phone ?? '—' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">E-mail</span><span>{{ $customer->email ?? '—' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Cadastro</span><span>{{ $customer->created_at->format('d/m/Y') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Total de pedidos</span><span class="font-bold">{{ $customer->orders->count() }}</span></div>
            </div>
        </div>
        <div class="card">
            <h3 class="font-semibold text-gray-800 mb-3">Estatísticas</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Total gasto</span>
                    <span class="font-bold text-green-600">R$ {{ number_format($customer->orders->where('payment_status','pago')->sum('total'), 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between"><span class="text-gray-500">Último pedido</span>
                    <span>{{ $customer->orders->first()?->created_at->format('d/m/Y') ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card overflow-hidden p-0">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h3 class="font-semibold text-gray-800">Histórico de Pedidos</h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Data</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customer->orders as $order)
                <tr>
                    <td><a href="{{ route('admin.orders.show', $order) }}" class="font-mono text-amber-700 hover:underline">{{ $order->code }}</a></td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td class="font-semibold">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                    <td><span class="badge {{ $order->status_color }}">{{ $order->status_label }}</span></td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-gray-400 py-6">Nenhum pedido</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.customers.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Voltar
        </a>
    </div>
</div>
@endsection
