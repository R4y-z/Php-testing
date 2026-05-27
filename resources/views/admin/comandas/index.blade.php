@extends('layouts.admin')
@section('title', 'Comandas')
@section('page-title', 'Comandas')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex gap-3">
        <span class="badge bg-green-100 text-green-700 text-sm px-3 py-1.5">
            {{ $comandas->where('status','aberta')->count() }} abertas
        </span>
        <span class="badge bg-yellow-100 text-yellow-700 text-sm px-3 py-1.5">
            {{ $comandas->where('status','fechamento')->count() }} em fechamento
        </span>
    </div>

    {{-- Botão Nova Comanda --}}
    <button onclick="document.getElementById('modal-nova').classList.remove('hidden')" class="btn-primary">
        <i class="fas fa-plus mr-2"></i>Nova Comanda
    </button>
</div>

{{-- Grid de Comandas --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @forelse($comandas as $comanda)
    <div class="card hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div>
                <p class="font-bold text-xl text-gray-800 font-mono">{{ $comanda->number }}</p>
                @if($comanda->table)
                <p class="text-sm text-gray-500"><i class="fas fa-chair mr-1"></i>{{ $comanda->table->display_name }}</p>
                @endif
                @if($comanda->customer_name)
                <p class="text-sm text-gray-500"><i class="fas fa-user mr-1"></i>{{ $comanda->customer_name }}</p>
                @endif
            </div>
            <span class="badge {{ $comanda->status_color }}">{{ $comanda->status_label }}</span>
        </div>

        <div class="border-t border-gray-100 pt-3">
            <div class="flex items-center justify-between mb-1">
                <span class="text-sm text-gray-500">{{ $comanda->items->where('status','!=','cancelado')->count() }} itens</span>
                <span class="font-bold text-gray-800">R$ {{ number_format($comanda->total, 2, ',', '.') }}</span>
            </div>
            <p class="text-xs text-gray-400">Aberta {{ $comanda->opened_at?->diffForHumans() }}</p>
        </div>

        <div class="flex gap-2 mt-3">
            <a href="{{ route('admin.comandas.show', $comanda) }}"
                class="flex-1 text-center py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-eye mr-1"></i>Ver
            </a>
            @if($comanda->isOpen())
            <a href="{{ route('admin.comandas.show', $comanda) }}"
                class="flex-1 text-center py-2 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-plus mr-1"></i>Itens
            </a>
            @endif
        </div>
    </div>
    @empty
    <div class="col-span-4 text-center py-16">
        <i class="fas fa-receipt text-6xl text-gray-200 mb-4 block"></i>
        <p class="text-gray-400 text-lg">Nenhuma comanda aberta</p>
        <button onclick="document.getElementById('modal-nova').classList.remove('hidden')"
            class="btn-primary mt-4">
            <i class="fas fa-plus mr-2"></i>Abrir primeira comanda
        </button>
    </div>
    @endforelse
</div>

{{-- Modal Nova Comanda --}}
<div id="modal-nova" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 text-lg">Nova Comanda</h3>
            <button onclick="document.getElementById('modal-nova').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form action="{{ route('admin.comandas.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="form-label">Mesa (opcional)</label>
                <select name="table_id" class="form-input">
                    <option value="">Sem mesa (balcão)</option>
                    @foreach($tables->where('status','disponivel') as $table)
                    <option value="{{ $table->id }}">{{ $table->display_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Nome do Cliente (opcional)</label>
                <input type="text" name="customer_name" class="form-input" placeholder="Ex: João Silva">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1">
                    <i class="fas fa-receipt mr-2"></i>Abrir Comanda
                </button>
                <button type="button" onclick="document.getElementById('modal-nova').classList.add('hidden')"
                    class="btn-secondary flex-1">Cancelar</button>
            </div>
        </form>
    </div>
</div>
@endsection
