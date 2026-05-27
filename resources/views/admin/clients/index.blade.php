@extends('layouts.admin')
@section('title', 'Clientes')
@section('page-title', 'Clientes')

@section('content')
<div class="flex items-center justify-between mb-5">
    <form method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nome, telefone..."
            class="form-input w-64">
        <button type="submit" class="btn-primary"><i class="fas fa-search"></i></button>
    </form>
    <button onclick="document.getElementById('modal-create').classList.remove('hidden')" class="btn-primary">
        <i class="fas fa-plus mr-2"></i>Novo Cliente
    </button>
</div>

<div class="card overflow-hidden p-0">
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Telefone</th>
                <th>E-mail</th>
                <th>Pedidos</th>
                <th>Cadastro</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
            <tr>
                <td class="font-medium text-gray-800">{{ $customer->name }}</td>
                <td>{{ $customer->phone ?? '—' }}</td>
                <td>{{ $customer->email ?? '—' }}</td>
                <td><span class="badge bg-blue-100 text-blue-700">{{ $customer->orders_count }}</span></td>
                <td class="text-gray-400 text-xs">{{ $customer->created_at->format('d/m/Y') }}</td>
                <td>
                    <a href="{{ route('admin.customers.show', $customer) }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-gray-400 py-8">Nenhum cliente</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $customers->withQueryString()->links() }}</div>
</div>

{{-- Modal novo cliente --}}
<div id="modal-create" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
        <div class="px-6 py-5 border-b flex items-center justify-between">
            <h3 class="font-semibold text-lg">Novo Cliente</h3>
            <button onclick="document.getElementById('modal-create').classList.add('hidden')" class="text-gray-400"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.customers.store') }}" method="POST" class="p-6 space-y-3">
            @csrf
            <div>
                <label class="form-label">Nome *</label>
                <input type="text" name="name" required class="form-input">
            </div>
            <div>
                <label class="form-label">Telefone</label>
                <input type="tel" name="phone" class="form-input" placeholder="(79) 99999-9999">
            </div>
            <div>
                <label class="form-label">E-mail</label>
                <input type="email" name="email" class="form-input">
            </div>
            <button type="submit" class="btn-primary w-full"><i class="fas fa-save mr-2"></i>Salvar</button>
        </form>
    </div>
</div>
@endsection
