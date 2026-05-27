@extends('layouts.admin')
@section('title', 'Estoque')
@section('page-title', 'Estoque')

@section('content')
@if($lowCount > 0)
<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-5 flex items-center gap-2">
    <i class="fas fa-exclamation-triangle text-red-500"></i>
    <strong>{{ $lowCount }} item(ns) com estoque abaixo do mínimo!</strong>
    <a href="{{ route('admin.stock.index', ['low_stock' => 1]) }}" class="ml-auto text-red-700 underline text-sm">Ver itens</a>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Formulário novo item --}}
    <div class="card">
        <h3 class="font-semibold text-gray-800 mb-4">Novo Item de Estoque</h3>
        <form action="{{ route('admin.stock.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="form-label">Nome *</label>
                <input type="text" name="name" required class="form-input" placeholder="Ex: Picanha">
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="form-label">Unidade</label>
                    <select name="unit" class="form-input">
                        <option value="un">un</option>
                        <option value="kg">kg</option>
                        <option value="L">L</option>
                        <option value="g">g</option>
                        <option value="cx">cx</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Qtd Inicial</label>
                    <input type="number" name="quantity" value="0" step="0.001" min="0" class="form-input">
                </div>
            </div>
            <div>
                <label class="form-label">Estoque Mínimo</label>
                <input type="number" name="min_quantity" value="0" step="0.001" min="0" class="form-input">
            </div>
            <div>
                <label class="form-label">Custo Unitário (R$)</label>
                <input type="number" name="cost_price" step="0.01" min="0" class="form-input" placeholder="0.00">
            </div>
            <div>
                <label class="form-label">Fornecedor</label>
                <input type="text" name="supplier" class="form-input" placeholder="Nome do fornecedor">
            </div>
            <button type="submit" class="btn-primary w-full"><i class="fas fa-plus mr-1"></i>Criar Item</button>
        </form>
    </div>

    {{-- Lista --}}
    <div class="lg:col-span-2 card overflow-hidden p-0">
        <div class="px-6 py-4 border-b flex items-center justify-between bg-gray-50">
            <h3 class="font-semibold text-gray-800">Itens em Estoque</h3>
            <form method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" class="form-input w-40" placeholder="Buscar...">
                <button class="btn-primary text-sm px-3"><i class="fas fa-search"></i></button>
            </form>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Estoque</th>
                    <th>Mínimo</th>
                    <th>Custo</th>
                    <th>Movimentar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr class="{{ $item->isLowStock() ? 'bg-red-50' : '' }}">
                    <td>
                        <p class="font-medium text-gray-800">{{ $item->name }}</p>
                        @if($item->supplier)
                        <p class="text-xs text-gray-400">{{ $item->supplier }}</p>
                        @endif
                    </td>
                    <td>
                        <span class="font-bold {{ $item->isLowStock() ? 'text-red-600' : 'text-gray-800' }}">
                            {{ number_format($item->quantity, 2, ',', '.') }} {{ $item->unit }}
                        </span>
                        @if($item->isLowStock())
                        <span class="badge bg-red-100 text-red-600 text-xs ml-1">Baixo</span>
                        @endif
                    </td>
                    <td>{{ number_format($item->min_quantity, 2, ',', '.') }} {{ $item->unit }}</td>
                    <td>{{ $item->cost_price ? 'R$ ' . number_format($item->cost_price, 2, ',', '.') : '—' }}</td>
                    <td>
                        <button onclick="openMovement({{ $item->id }}, '{{ addslashes($item->name) }}')"
                            class="bg-amber-50 hover:bg-amber-100 text-amber-700 px-3 py-1.5 rounded-lg text-sm transition-colors">
                            <i class="fas fa-exchange-alt mr-1"></i>Mov.
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-gray-400 py-8">Nenhum item cadastrado</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $items->withQueryString()->links() }}</div>
    </div>
</div>

{{-- Modal Movimentação --}}
<div id="modal-movement" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
        <div class="px-6 py-5 border-b flex items-center justify-between">
            <h3 class="font-semibold text-lg">Movimentação: <span id="m-item-name"></span></h3>
            <button onclick="document.getElementById('modal-movement').classList.add('hidden')" class="text-gray-400"><i class="fas fa-times"></i></button>
        </div>
        <form id="movement-form" action="" method="POST" class="p-6 space-y-4">
            @csrf @method('POST')
            <div>
                <label class="form-label">Tipo *</label>
                <select name="type" class="form-input">
                    <option value="entrada">Entrada</option>
                    <option value="saida">Saída</option>
                    <option value="ajuste">Ajuste (define novo saldo)</option>
                </select>
            </div>
            <div>
                <label class="form-label">Quantidade *</label>
                <input type="number" name="quantity" step="0.001" min="0.001" required class="form-input text-xl text-center" placeholder="0.000">
            </div>
            <div>
                <label class="form-label">Custo Unitário (R$)</label>
                <input type="number" name="unit_cost" step="0.01" min="0" class="form-input">
            </div>
            <div>
                <label class="form-label">Motivo</label>
                <input type="text" name="reason" class="form-input" placeholder="Ex: Compra, Uso, Inventário">
            </div>
            <button type="submit" class="btn-primary w-full">Registrar</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openMovement(id, name) {
    document.getElementById('m-item-name').textContent = name;
    document.getElementById('movement-form').action = `/admin/estoque/${id}/movimento`;
    document.getElementById('modal-movement').classList.remove('hidden');
}
</script>
@endpush
@endsection
