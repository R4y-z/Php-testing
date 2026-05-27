@extends('layouts.admin')
@section('title', 'Relatórios')
@section('page-title', 'Relatórios')

@section('content')
{{-- Filtro de período --}}
<div class="card mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="form-label">Data Inicial</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="form-input w-40">
        </div>
        <div>
            <label class="form-label">Data Final</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="form-input w-40">
        </div>
        <button type="submit" class="btn-primary">
            <i class="fas fa-filter mr-1"></i>Filtrar
        </button>
        <a href="{{ route('admin.reports.index', ['start_date' => today()->format('Y-m-d'), 'end_date' => today()->format('Y-m-d')]) }}" class="btn-secondary text-sm">Hoje</a>
        <a href="{{ route('admin.reports.index', ['start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => today()->format('Y-m-d')]) }}" class="btn-secondary text-sm">Este mês</a>
    </form>
</div>

{{-- Resumo --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card">
        <p class="text-xs text-gray-400 mb-1">Faturamento</p>
        <p class="text-2xl font-bold text-green-600">R$ {{ number_format($revenue, 2, ',', '.') }}</p>
    </div>
    <div class="card">
        <p class="text-xs text-gray-400 mb-1">Pedidos</p>
        <p class="text-2xl font-bold text-gray-800">{{ $ordersCount }}</p>
    </div>
    <div class="card">
        <p class="text-xs text-gray-400 mb-1">Ticket Médio</p>
        <p class="text-2xl font-bold text-amber-600">R$ {{ number_format($avgTicket, 2, ',', '.') }}</p>
    </div>
    <div class="card">
        <p class="text-xs text-gray-400 mb-1">KG Vendido</p>
        <p class="text-2xl font-bold text-purple-600">{{ number_format($kgSold, 3, ',', '.') }} kg</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Top produtos --}}
    <div class="card">
        <h3 class="font-semibold text-gray-800 mb-4">Top 10 Produtos</h3>
        <div class="space-y-2">
            @forelse($topProducts as $i => $product)
            <div class="flex items-center gap-3">
                <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-700 text-xs flex items-center justify-center font-bold flex-shrink-0">{{ $i+1 }}</span>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">{{ $product->product_name }}</p>
                    <p class="text-xs text-gray-400">{{ number_format($product->total_qty, 2, ',', '.') }} × vendidos</p>
                </div>
                <p class="text-sm font-bold text-green-600">R$ {{ number_format($product->total_revenue, 2, ',', '.') }}</p>
            </div>
            @empty
            <p class="text-center text-gray-400 py-4">Sem dados no período</p>
            @endforelse
        </div>
    </div>

    {{-- Por forma de pagamento --}}
    <div class="card">
        <h3 class="font-semibold text-gray-800 mb-4">Por Forma de Pagamento</h3>
        @php
            $methodLabels = ['dinheiro' => 'Dinheiro', 'pix' => 'PIX', 'cartao_credito' => 'Cartão Crédito', 'cartao_debito' => 'Cartão Débito'];
        @endphp
        <div class="space-y-3">
            @forelse($byPayment as $payment)
            @php
                $pct = $revenue > 0 ? ($payment->total / $revenue * 100) : 0;
            @endphp
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-700">{{ $methodLabels[$payment->method] ?? $payment->method }}</span>
                    <span class="font-semibold">R$ {{ number_format($payment->total, 2, ',', '.') }}</span>
                </div>
                <div class="h-2 bg-gray-100 rounded-full">
                    <div class="h-2 bg-amber-500 rounded-full" style="width: {{ $pct }}%"></div>
                </div>
                <p class="text-xs text-gray-400">{{ $payment->count }} pagamentos</p>
            </div>
            @empty
            <p class="text-center text-gray-400 py-4">Sem dados</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Faturamento por dia --}}
@if($dailyRevenue->isNotEmpty())
<div class="card">
    <h3 class="font-semibold text-gray-800 mb-4">Faturamento Diário</h3>
    <div class="overflow-x-auto">
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Pedidos</th>
                    <th>Faturamento</th>
                    <th>Ticket Médio</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dailyRevenue as $day)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($day->date)->format('d/m/Y') }}</td>
                    <td>{{ $day->count }}</td>
                    <td class="font-semibold text-green-600">R$ {{ number_format($day->total, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($day->count > 0 ? $day->total / $day->count : 0, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
