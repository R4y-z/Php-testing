@extends('layouts.admin')
@section('title', 'Novo Produto')
@section('page-title', 'Novo Produto')

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="form-label">Nome *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="form-input"
                        placeholder="Ex: Espeto de Frango">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">Categoria *</label>
                    <select name="category_id" required class="form-input">
                        <option value="">Selecione...</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">Tipo *</label>
                    <select name="type" id="type_select" required class="form-input" onchange="toggleKgFields()">
                        <option value="unitario" {{ old('type','unitario') == 'unitario' ? 'selected' : '' }}>Unitário</option>
                        <option value="kg" {{ old('type') == 'kg' ? 'selected' : '' }}>Por KG</option>
                    </select>
                </div>

                <div>
                    <label class="form-label">Preço (R$) *</label>
                    <input type="number" name="price" value="{{ old('price') }}" required step="0.01" min="0"
                        class="form-input" placeholder="0.00">
                    <p id="kg-hint" class="text-xs text-gray-400 mt-1 hidden">Preço por quilograma</p>
                    @error('price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">Ordem de exibição</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="form-input">
                </div>

                <div class="sm:col-span-2">
                    <label class="form-label">Descrição</label>
                    <textarea name="description" rows="3" class="form-input" placeholder="Descrição do produto...">{{ old('description') }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label class="form-label">Imagem</label>
                    <input type="file" name="image" accept="image/*" class="form-input" id="image-input" onchange="previewImage(this)">
                    <img id="image-preview" src="" class="mt-2 h-32 rounded-lg object-cover hidden">
                </div>

                <div class="sm:col-span-2 space-y-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="active" value="1" {{ old('active', true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-amber-500">
                        <span class="text-sm font-medium text-gray-700">Produto ativo</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="available" value="1" {{ old('available', true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-amber-500">
                        <span class="text-sm font-medium text-gray-700">Disponível no cardápio</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="track_stock" value="1" {{ old('track_stock') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-amber-500">
                        <span class="text-sm font-medium text-gray-700">Controlar estoque</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Salvar Produto
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
function toggleKgFields() {
    const isKg = document.getElementById('type_select').value === 'kg';
    document.getElementById('kg-hint').classList.toggle('hidden', !isKg);
}
function previewImage(input) {
    const preview = document.getElementById('image-preview');
    if (input.files && input.files[0]) {
        preview.src = URL.createObjectURL(input.files[0]);
        preview.classList.remove('hidden');
    }
}
toggleKgFields();
</script>
@endsection
