@extends('layouts.admin')
@section('title', 'Mesas')
@section('page-title', 'Mesas')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    {{-- Mapa de mesas --}}
    <div class="lg:col-span-3">
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-3">
            @foreach($tables as $table)
            <div class="card border-2 {{ $table->status_color }} hover:shadow-md transition-shadow cursor-pointer"
                onclick="openTableModal({{ $table->id }}, '{{ $table->number }}', '{{ $table->name }}', {{ $table->capacity }}, '{{ $table->status }}', '{{ $table->location }}')">

                <div class="text-center">
                    <div class="w-12 h-12 mx-auto mb-2 rounded-xl {{ $table->status === 'disponivel' ? 'bg-green-100' : ($table->status === 'ocupada' ? 'bg-red-100' : 'bg-yellow-100') }} flex items-center justify-center">
                        <i class="fas fa-chair text-2xl {{ $table->status === 'disponivel' ? 'text-green-600' : ($table->status === 'ocupada' ? 'text-red-500' : 'text-yellow-600') }}"></i>
                    </div>
                    <p class="font-bold text-lg text-gray-800">{{ $table->number }}</p>
                    @if($table->name)
                    <p class="text-xs text-gray-500">{{ $table->name }}</p>
                    @endif
                    <p class="text-xs text-gray-400"><i class="fas fa-users mr-1"></i>{{ $table->capacity }} lugares</p>

                    @php $comanda = $table->activeComanda; @endphp
                    @if($comanda)
                    <div class="mt-2 bg-red-50 border border-red-200 rounded-lg p-1.5">
                        <p class="text-xs font-bold text-red-700">{{ $comanda->number }}</p>
                        <p class="text-xs text-red-500">R$ {{ number_format($comanda->total, 2, ',', '.') }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach

            {{-- Botão nova mesa --}}
            <button onclick="document.getElementById('modal-create').classList.remove('hidden')"
                class="card border-2 border-dashed border-gray-300 hover:border-amber-400 flex flex-col items-center justify-center min-h-[130px] transition-colors">
                <i class="fas fa-plus text-2xl text-gray-300 mb-1"></i>
                <span class="text-sm text-gray-400">Nova Mesa</span>
            </button>
        </div>
    </div>

    {{-- Legenda --}}
    <div class="card h-fit">
        <h3 class="font-semibold text-gray-800 mb-3">Legenda</h3>
        <div class="space-y-2">
            @foreach(['disponivel' => ['color' => 'green', 'label' => 'Disponível'], 'ocupada' => ['color' => 'red', 'label' => 'Ocupada'], 'reservada' => ['color' => 'yellow', 'label' => 'Reservada'], 'manutencao' => ['color' => 'gray', 'label' => 'Manutenção']] as $status => $info)
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded-full bg-{{ $info['color'] }}-400"></div>
                <span class="text-sm text-gray-600">{{ $info['label'] }}</span>
                <span class="ml-auto text-sm font-bold text-gray-800">{{ $tables->where('status', $status)->count() }}</span>
            </div>
            @endforeach
        </div>
        <div class="border-t mt-3 pt-3">
            <p class="text-sm text-gray-500">Total: {{ $tables->count() }} mesas</p>
        </div>
    </div>
</div>

{{-- Modal editar mesa --}}
<div id="modal-table" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
        <div class="px-6 py-5 border-b flex items-center justify-between">
            <h3 class="font-semibold text-lg">Mesa <span id="modal-table-num"></span></h3>
            <button onclick="document.getElementById('modal-table').classList.add('hidden')" class="text-gray-400">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="table-status-form" action="" method="POST" class="p-6 space-y-4">
            @csrf @method('PATCH')
            <input type="hidden" name="number" id="m-number">
            <input type="hidden" name="name" id="m-name">
            <input type="hidden" name="capacity" id="m-capacity">
            <input type="hidden" name="location" id="m-location">
            <div>
                <label class="form-label">Alterar Status</label>
                <select name="status" id="m-status" class="form-input">
                    <option value="disponivel">Disponível</option>
                    <option value="ocupada">Ocupada</option>
                    <option value="reservada">Reservada</option>
                    <option value="manutencao">Manutenção</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary flex-1">Salvar</button>
                <a id="modal-comanda-link" href="#" class="btn-secondary text-center flex-1 text-sm">
                    <i class="fas fa-receipt mr-1"></i>Comanda
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Modal nova mesa --}}
<div id="modal-create" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
        <div class="px-6 py-5 border-b flex items-center justify-between">
            <h3 class="font-semibold text-lg">Nova Mesa</h3>
            <button onclick="document.getElementById('modal-create').classList.add('hidden')" class="text-gray-400"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.tables.store') }}" method="POST" class="p-6 space-y-3">
            @csrf
            <div>
                <label class="form-label">Número *</label>
                <input type="text" name="number" required class="form-input" placeholder="01">
            </div>
            <div>
                <label class="form-label">Nome (opcional)</label>
                <input type="text" name="name" class="form-input" placeholder="Ex: Mesa VIP">
            </div>
            <div>
                <label class="form-label">Capacidade *</label>
                <input type="number" name="capacity" value="4" min="1" max="20" required class="form-input">
            </div>
            <div>
                <label class="form-label">Localização</label>
                <input type="text" name="location" class="form-input" placeholder="Salão Principal">
            </div>
            <button type="submit" class="btn-primary w-full"><i class="fas fa-plus mr-2"></i>Criar Mesa</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openTableModal(id, number, name, capacity, status, location) {
    document.getElementById('modal-table-num').textContent = number;
    document.getElementById('m-number').value = number;
    document.getElementById('m-name').value = name;
    document.getElementById('m-capacity').value = capacity;
    document.getElementById('m-location').value = location;
    document.getElementById('m-status').value = status;
    document.getElementById('table-status-form').action = `/admin/mesas/${id}`;
    document.getElementById('modal-comanda-link').href = `{{ url('admin/comandas') }}?table=${id}`;
    document.getElementById('modal-table').classList.remove('hidden');
}
</script>
@endpush
@endsection
