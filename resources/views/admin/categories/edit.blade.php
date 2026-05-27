@extends('layouts.admin')
@section('title', 'Editar Categoria')
@section('page-title', 'Editar: ' . $category->name)

@section('content')
<div class="max-w-lg">
    <div class="card">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="form-label">Nome *</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="form-input">
            </div>
            <div>
                <label class="form-label">Descrição</label>
                <textarea name="description" rows="2" class="form-input">{{ old('description', $category->description) }}</textarea>
            </div>
            <div>
                <label class="form-label">Ordem</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Imagem atual</label>
                @if($category->image)
                <img src="{{ asset('storage/' . $category->image) }}" class="h-20 rounded-lg object-cover mb-2">
                @endif
                <input type="file" name="image" accept="image/*" class="form-input">
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="active" value="1" {{ old('active', $category->active) ? 'checked' : '' }} class="rounded border-gray-300 text-amber-500">
                <span class="text-sm font-medium text-gray-700">Categoria ativa</span>
            </label>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary"><i class="fas fa-save mr-2"></i>Salvar</button>
                <a href="{{ route('admin.categories.index') }}" class="btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
