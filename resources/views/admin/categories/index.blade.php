@extends('layouts.admin')
@section('title', 'Categorias')
@section('page-title', 'Categorias')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Form Nova Categoria --}}
    <div class="card">
        <h3 class="font-semibold text-gray-800 mb-4">Nova Categoria</h3>
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="form-label">Nome *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="form-input" placeholder="Ex: Espetos">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Descrição</label>
                <textarea name="description" rows="2" class="form-input" placeholder="Opcional">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="form-label">Ordem</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Imagem (opcional)</label>
                <input type="file" name="image" accept="image/*" class="form-input">
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="active" value="1" checked class="rounded border-gray-300 text-amber-500">
                <span class="text-sm text-gray-700">Categoria ativa</span>
            </label>
            <button type="submit" class="btn-primary w-full">
                <i class="fas fa-plus mr-2"></i>Criar Categoria
            </button>
        </form>
    </div>

    {{-- Lista --}}
    <div class="lg:col-span-2 card overflow-hidden p-0">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800">Categorias ({{ $categories->count() }})</h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Produtos</th>
                    <th>Ordem</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" class="w-8 h-8 rounded-lg object-cover">
                            @else
                            <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-tag text-amber-600 text-xs"></i>
                            </div>
                            @endif
                            <span class="font-medium text-gray-800">{{ $category->name }}</span>
                        </div>
                    </td>
                    <td><span class="badge bg-blue-100 text-blue-700">{{ $category->products_count }}</span></td>
                    <td>{{ $category->sort_order }}</td>
                    <td>
                        <span class="badge {{ $category->active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $category->active ? 'Ativa' : 'Inativa' }}
                        </span>
                    </td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline"
                                onsubmit="return confirm('Excluir categoria {{ $category->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-gray-400 py-8">Nenhuma categoria</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
