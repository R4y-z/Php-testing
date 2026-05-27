@extends('layouts.admin')
@section('title', 'Editar Produto')
@section('page-title', 'Editar: ' . $product->name)

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="form-label">Nome *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="form-input">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">Categoria *</label>
                    <select name="category_id" required class="form-input">
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Tipo *</label>
                    <select name="type" required class="form-input">
                        <option value="unitario" {{ old('type', $product->type) == 'unitario' ? 'selected' : '' }}>Unitário</option>
                        <option value="kg" {{ old('type', $product->type) == 'kg' ? 'selected' : '' }}>Por KG</option>
                    </select>
                </div>

                <div>
                    <label class="form-label">Preço (R$) *</label>
                    <input type="number" name="price" value="{{ old('price', $product->price) }}" required step="0.01" min="0" class="form-input">
                </div>

                <div>
                    <label class="form-label">Ordem</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $product->sort_order) }}" class="form-input">
                </div>

                <div class="sm:col-span-2">
                    <label class="form-label">Descrição</label>
                    <textarea name="description" rows="3" class="form-input">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label class="form-label">Imagem atual</label>
                    @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" class="h-32 rounded-lg object-cover mb-2">
                    @endif
                    <input type="file" name="image" accept="image/*" class="form-input">
                    <p class="text-xs text-gray-400 mt-1">Deixe em branco para manter a imagem atual</p>
                </div>

                <div class="sm:col-span-2 space-y-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="active" value="1" {{ old('active', $product->active) ? 'checked' : '' }} class="rounded border-gray-300 text-amber-500">
                        <span class="text-sm font-medium text-gray-700">Produto ativo</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="available" value="1" {{ old('available', $product->available) ? 'checked' : '' }} class="rounded border-gray-300 text-amber-500">
                        <span class="text-sm font-medium text-gray-700">Disponível no cardápio</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary"><i class="fas fa-save mr-2"></i>Salvar</button>
                <a href="{{ route('admin.products.index') }}" class="btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
