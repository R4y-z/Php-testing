@extends('layouts.admin')
@section('title', 'Caixa')
@section('page-title', 'Caixa')

@section('content')

{{-- Status do caixa --}}
<div class="card mb-6 {{ $currentSession ? 'border-green-200' : 'border-red-200' }}">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 {{ $currentSession ? 'bg-green-100' : 'bg-red-100' }} rounded-full flex items-center justify-center">
                <i class="fas fa-cash-register text-2xl {{ $currentSession ? 'text-green-600' : 'text-red-500' }}"></i>
            </div>
            <div>
                <p class="font-bold text-xl {{ $currentSession ? 'text-green-700' : 'text-red-600' }}">
                    Caixa {{ $currentSession ? 'ABERTO' : 'FECHADO' }}
                </p>
                @if($currentSession)
                <p class="text-sm text-gray-500">
                    Aberto por {{ $currentSession->openedBy->name }} às {{ $currentSession->opened_at->format('H:i') }}
                </p>
                <p class="text-sm text-gray-500">Saldo inicial: R$ {{ number_format($currentSession->opening_balance, 2, ',', '.') }}</p>
                @else
                <p class="text-sm text-gray-400">Nenhum caixa aberto hoje</p>
                @endif
            </div>
        </div>

        @if(!$currentSession)
        <button onclick="document.getElementById('modal-open').classList.remove('hidden')" class="btn-primary">
            <i class="fas fa-unlock mr-2"></i>Abrir Caixa
        </button>
        @else
        <button onclick="document.getElementById('modal-close').classList.remove('hidden')" class="btn-danger">
            <i class="fas fa-lock mr-2"></i>Fechar Caixa
        </button>
        @endif
    </div>
</div>

@if($currentSession)
{{-- Totais por forma de pagamento --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
        $methods = ['dinheiro' => ['label' => 'Dinheiro', 'icon' => 'fa-money-bill', 'color' => 'green'],
                    'pix' => ['label' => 'PIX', 'icon' => 'fa-qrcode', 'color' => 'blue'],
                    'cartao_credito' => ['label' => 'Crédito', 'icon' => 'fa-credit-card', 'color' => 'purple'],
                    'cartao_debito' => ['label' => 'Débito', 'icon' => 'fa-credit-card', 'color' => 'indigo']];
    @endphp
    @foreach($methods as $method => $info)
    @php
        $payment = $todayPayments->firstWhere('method', $method);
        $total = $payment ? $payment->total : 0;
        $count = $payment ? $payment->count : 0;
    @endphp
    <div class="card">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-{{ $info['color'] }}-100 rounded-xl flex items-center justify-center">
                <i class="fas {{ $info['icon'] }} text-{{ $info['color'] }}-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400">{{ $info['label'] }}</p>
                <p class="font-bold text-gray-800">R$ {{ number_format($total, 2, ',', '.') }}</p>
                <p class="text-xs text-gray-400">{{ $count }} pagamento(s)</p>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Comandas Abertas --}}
    <div class="card overflow-hidden p-0">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h3 class="font-semibold text-gray-800">Comandas Abertas ({{ $openComandas->count() }})</h3>
        </div>
        @forelse($openComandas as $comanda)
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between hover:bg-gray-50">
            <div>
                <p class="font-bold font-mono text-amber-700">{{ $comanda->number }}</p>
                @if($comanda->table)
                <p class="text-xs text-gray-400">{{ $comanda->table->display_name }}</p>
                @endif
                @if($comanda->customer_name)
                <p class="text-xs text-gray-400">{{ $comanda->customer_name }}</p>
                @endif
            </div>
            <div class="text-right">
                <p class="font-bold text-lg text-gray-800">R$ {{ number_format($comanda->total, 2, ',', '.') }}</p>
                <p class="text-xs text-gray-400">{{ $comanda->opened_at?->diffForHumans() }}</p>
            </div>
            <a href="{{ route('admin.comandas.show', $comanda) }}"
                class="ml-4 bg-amber-50 hover:bg-amber-100 text-amber-700 px-3 py-2 rounded-lg text-sm transition-colors">
                <i class="fas fa-external-link-alt"></i>
            </a>
        </div>
        @empty
        <div class="px-6 py-8 text-center text-gray-400">
            <i class="fas fa-receipt text-3xl mb-2 block text-gray-200"></i>
            Nenhuma comanda aberta
        </div>
        @endforelse
    </div>

    {{-- Recentes fechadas hoje --}}
    <div class="card overflow-hidden p-0">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h3 class="font-semibold text-gray-800">Finalizadas Hoje</h3>
        </div>
        @forelse($recentComandas as $comanda)
        <div class="px-6 py-3 border-b border-gray-100 flex items-center justify-between">
            <div>
                <p class="font-mono font-bold text-gray-700">{{ $comanda->number }}</p>
                @if($comanda->table)
                <p class="text-xs text-gray-400">{{ $comanda->table->display_name }}</p>
                @endif
            </div>
            <p class="font-semibold text-green-600">R$ {{ number_format($comanda->total, 2, ',', '.') }}</p>
        </div>
        @empty
        <div class="px-6 py-8 text-center text-gray-400 text-sm">Nenhuma finalizada hoje</div>
        @endforelse
    </div>
</div>

{{-- Modal Abrir Caixa --}}
<div id="modal-open" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
        <div class="px-6 py-5 border-b flex items-center justify-between">
            <h3 class="font-semibold text-lg">Abrir Caixa</h3>
            <button onclick="document.getElementById('modal-open').classList.add('hidden')" class="text-gray-400"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.cash.open') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="form-label">Saldo Inicial (R$) *</label>
                <input type="number" name="opening_balance" value="0" step="0.01" min="0" required
                    class="form-input text-2xl font-bold text-center">
            </div>
            <div>
                <label class="form-label">Observações</label>
                <input type="text" name="notes" class="form-input">
            </div>
            <button type="submit" class="btn-primary w-full py-3 text-lg">
                <i class="fas fa-unlock mr-2"></i>Abrir Caixa
            </button>
        </form>
    </div>
</div>

{{-- Modal Fechar Caixa --}}
<div id="modal-close" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
        <div class="px-6 py-5 border-b flex items-center justify-between">
            <h3 class="font-semibold text-lg">Fechar Caixa</h3>
            <button onclick="document.getElementById('modal-close').classList.add('hidden')" class="text-gray-400"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.cash.close') }}" method="POST" class="p-6 space-y-4">
            @csrf @method('PATCH')
            <div>
                <label class="form-label">Saldo em Caixa (R$) *</label>
                <input type="number" name="closing_balance" step="0.01" min="0" required
                    class="form-input text-2xl font-bold text-center" placeholder="0.00">
            </div>
            <div>
                <label class="form-label">Observações</label>
                <input type="text" name="notes" class="form-input">
            </div>
            <button type="submit" class="btn-danger w-full py-3 text-lg"
                onclick="return confirm('Confirmar fechamento do caixa?')">
                <i class="fas fa-lock mr-2"></i>Fechar Caixa
            </button>
        </form>
    </div>
</div>
@endsection
