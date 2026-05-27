@extends('layouts.admin')
@section('title', 'Configurações')
@section('page-title', 'Configurações')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
        @csrf @method('PUT')

        {{-- Geral --}}
        <div class="card space-y-4">
            <h3 class="font-semibold text-gray-800 text-lg border-b pb-3">
                <i class="fas fa-store mr-2 text-amber-500"></i>Informações do Restaurante
            </h3>
            @foreach($settings['general'] ?? [] as $setting)
            <div>
                <label class="form-label">{{ $setting->label }}</label>
                <input type="text" name="{{ $setting->key }}" value="{{ $setting->value }}" class="form-input">
            </div>
            @endforeach
        </div>

        {{-- Horário --}}
        <div class="card space-y-4">
            <h3 class="font-semibold text-gray-800 text-lg border-b pb-3">
                <i class="fas fa-clock mr-2 text-amber-500"></i>Horário de Funcionamento
            </h3>
            @foreach($settings['hours'] ?? [] as $setting)
            <div>
                <label class="form-label">{{ $setting->label }}</label>
                <input type="{{ in_array($setting->key, ['open_time','close_time']) ? 'time' : 'text' }}"
                    name="{{ $setting->key }}" value="{{ $setting->value }}" class="form-input">
            </div>
            @endforeach
        </div>

        {{-- Delivery --}}
        <div class="card space-y-4">
            <h3 class="font-semibold text-gray-800 text-lg border-b pb-3">
                <i class="fas fa-motorcycle mr-2 text-amber-500"></i>Delivery
            </h3>
            @foreach($settings['delivery'] ?? [] as $setting)
            <div>
                <label class="form-label">{{ $setting->label }}</label>
                @if($setting->type === 'boolean')
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="{{ $setting->key }}" value="1" {{ $setting->value ? 'checked' : '' }}
                        class="rounded border-gray-300 text-amber-500 w-5 h-5">
                    <span class="text-sm text-gray-600">Ativo</span>
                </label>
                @else
                <input type="{{ $setting->type === 'decimal' ? 'number' : 'text' }}"
                    name="{{ $setting->key }}" value="{{ $setting->value }}"
                    step="{{ $setting->type === 'decimal' ? '0.01' : '' }}" class="form-input">
                @endif
            </div>
            @endforeach
        </div>

        {{-- Pagamento --}}
        <div class="card space-y-4">
            <h3 class="font-semibold text-gray-800 text-lg border-b pb-3">
                <i class="fas fa-credit-card mr-2 text-amber-500"></i>Formas de Pagamento
            </h3>
            @foreach($settings['payment'] ?? [] as $setting)
            <div>
                <label class="form-label">{{ $setting->label }}</label>
                @if($setting->type === 'boolean')
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="{{ $setting->key }}" value="1" {{ $setting->value ? 'checked' : '' }}
                        class="rounded border-gray-300 text-amber-500 w-5 h-5">
                    <span class="text-sm text-gray-600">Aceitar esta forma</span>
                </label>
                @else
                <input type="text" name="{{ $setting->key }}" value="{{ $setting->value }}" class="form-input">
                @endif
            </div>
            @endforeach
        </div>

        {{-- KG --}}
        <div class="card space-y-4">
            <h3 class="font-semibold text-gray-800 text-lg border-b pb-3">
                <i class="fas fa-weight-scale mr-2 text-amber-500"></i>Self-Service por KG
            </h3>
            @foreach($settings['kg'] ?? [] as $setting)
            <div>
                <label class="form-label">{{ $setting->label }}</label>
                @if($setting->type === 'boolean')
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="{{ $setting->key }}" value="1" {{ $setting->value ? 'checked' : '' }}
                        class="rounded border-gray-300 text-amber-500 w-5 h-5">
                    <span class="text-sm text-gray-600">Ativo</span>
                </label>
                @else
                <input type="number" step="0.01" name="{{ $setting->key }}" value="{{ $setting->value }}" class="form-input">
                @endif
            </div>
            @endforeach
        </div>

        <button type="submit" class="btn-primary px-8 py-3 text-base">
            <i class="fas fa-save mr-2"></i>Salvar Configurações
        </button>
    </form>
</div>
@endsection
