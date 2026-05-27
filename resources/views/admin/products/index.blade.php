@extends('layouts.admin')
@section('title', 'Produtos')
@section('page-title', 'Produtos')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex gap-3">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar produto..."
                class="form-input w-48">
            <select name="category_id" class="form-input w-40">
                <option value="">Todas categorias</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn-primary">
        <i class="fas fa-plus mr-2"></i>Novo Produto
    </a>
</div>

<div class="card overflow-hidden p-0">
    <table>
        <thead>
            <tr>
                <th class="w-16">Foto</th>
                <th>Nome</th>
                <th>Categoria</th>
                <th>Preço</th>
                <th>Tipo</th>
                <th>Disponível</th>
                <th>Ativo</th>
                <th class="w-28">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr>
                <td>
                    @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" class="w-10 h-10 rounded-lg object-cover">
                    @else
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-utensils text-gray-400 text-xs"></i>
                    </div>
                    @endif
                </td>
                <td>
                    <p class="font-medium text-gray-800">{{ $product->name }}</p>
                    @if($product->description)
                    <p class="text-xs text-gray-400 truncate max-w-xs">{{ $product->description }}</p>
                    @endif
                </td>
                <td>{{ $product->category->name }}</td>
                <td class="font-semibold text-gray-800">{{ $product->formatted_price }}</td>
                <td>
                    <span class="badge {{ $product->type === 'kg' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ $product->type === 'kg' ? 'Por KG' : 'Unitário' }}
                    </span>
                </td>
                <td>
                    <form action="{{ route('admin.products.toggle', $product) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        <button type="submit" class="badge {{ $product->available ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }} cursor-pointer">
                            {{ $product->available ? 'Sim' : 'Não' }}
                        </button>
                    </form>
                </td>
                <td>
                    <span class="badge {{ $product->active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $product->active ? 'Ativo' : 'Inativo' }}
                    </span>
                </td>
                <td>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-800" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline"
                            onsubmit="return confirm('Excluir produto {{ $product->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center text-gray-400 py-10">
                    <i class="fas fa-utensils text-3xl mb-2 block"></i>
                    Nenhum produto encontrado
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $products->withQueryString()->links() }}</div>
</div>
@endsection
