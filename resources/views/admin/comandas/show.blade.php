@extends('layouts.admin')
@section('title', 'Comanda ' . $comanda->number)
@section('page-title', 'Comanda ' . $comanda->number)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Coluna esquerda: Produtos --}}
    @if($comanda->isOpen())
    <div class="lg:col-span-1">
        <div class="card sticky top-4">
            <h3 class="font-semibold text-gray-800 mb-4">Adicionar Item</h3>

            <form action="{{ route('admin.comandas.add-item', $comanda) }}" method="POST" class="space-y-3" id="add-item-form">
                @csrf

                {{-- Busca --}}
                <input type="text" id="product-search" placeholder="Buscar produto..." class="form-input"
                    oninput="filterProducts(this.value)">

                {{-- Lista de produtos --}}
                <div id="product-list" class="max-h-64 overflow-y-auto space-y-1 border border-gray-200 rounded-lg p-1">
                    @foreach($products as $category => $categoryProducts)
                    <p class="text-xs font-semibold text-gray-400 uppercase px-2 py-1">{{ $category }}</p>
                    @foreach($categoryProducts as $product)
                    <label class="flex items-center gap-2 px-2 py-2 rounded-lg hover:bg-amber-50 cursor-pointer product-item" data-name="{{ strtolower($product->name) }}">
                        <input type="radio" name="product_id" value="{{ $product->id }}"
                            data-price="{{ $product->price }}" data-type="{{ $product->type }}"
                            class="text-amber-500" onchange="selectProduct(this)">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">{{ $product->name }}</p>
                            <p class="text-xs text-gray-400">{{ $product->formatted_price }}</p>
                        </div>
                    </label>
                    @endforeach
                    @endforeach
                </div>

                {{-- Quantidade --}}
                <div>
                    <label class="form-label">Quantidade / Peso (kg)</label>
                    <div class="flex gap-2">
                        <input type="number" name="quantity" id="quantity" value="1" step="0.001" min="0.001"
                            required class="form-input text-lg font-bold">
                        <span id="unit-label" class="flex items-center px-3 bg-gray-100 rounded-lg text-gray-600 font-medium text-sm">un</span>
                    </div>
                    <p id="total-preview" class="text-xs text-amber-700 font-semibold mt-1"></p>
                </div>

                <div>
                    <label class="form-label">Observação</label>
                    <input type="text" name="notes" class="form-input" placeholder="Ex: sem sal, bem passado...">
                </div>

                <button type="submit" class="btn-primary w-full">
                    <i class="fas fa-plus mr-2"></i>Adicionar Item
                </button>
            </form>
        </div>
    </div>
    @endif

    {{-- Coluna direita: Comanda --}}
    <div class="{{ $comanda->isOpen() ? 'lg:col-span-2' : 'lg:col-span-3' }}">
        {{-- Info da comanda --}}
        <div class="card mb-4">
            <div class="flex items-start justify-between">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm flex-1">
                    <div>
                        <p class="text-gray-400 text-xs">Número</p>
                        <p class="font-bold text-xl font-mono">{{ $comanda->number }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs">Status</p>
                        <span class="badge {{ $comanda->status_color }}">{{ $comanda->status_label }}</span>
                    </div>
                    @if($comanda->table)
                    <div>
                        <p class="text-gray-400 text-xs">Mesa</p>
                        <p class="font-medium">{{ $comanda->table->display_name }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-gray-400 text-xs">Aberta em</p>
                        <p class="font-medium">{{ $comanda->opened_at?->format('d/m H:i') }}</p>
                    </div>
                </div>
                @if($comanda->isOpen())
                <form action="{{ route('admin.comandas.cancel', $comanda) }}" method="POST"
                    onsubmit="return confirm('Cancelar esta comanda?')">
                    @csrf @method('DELETE')
                    <button class="text-red-500 hover:text-red-700 text-sm ml-4">
                        <i class="fas fa-times mr-1"></i>Cancelar
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Itens --}}
        <div class="card overflow-hidden p-0">
            <div class="px-6 py-4 border-b bg-gray-50 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Itens da Comanda</h3>
                <span class="text-sm text-gray-500">{{ $comanda->items->where('status','!=','cancelado')->count() }} itens</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Qtd</th>
                        <th>Unitário</th>
                        <th>Total</th>
                        @if($comanda->isOpen())
                        <th>Ação</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($comanda->items as $item)
                    <tr class="{{ $item->status === 'cancelado' ? 'opacity-40 line-through' : '' }}">
                        <td>
                            <p class="font-medium text-gray-800">{{ $item->product_name }}</p>
                            @if($item->notes)
                            <p class="text-xs text-gray-400">{{ $item->notes }}</p>
                            @endif
                        </td>
                        <td>{{ $item->formatted_quantity }}</td>
                        <td>R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                        <td class="font-semibold">R$ {{ number_format($item->total, 2, ',', '.') }}</td>
                        @if($comanda->isOpen())
                        <td>
                            @if($item->status !== 'cancelado')
                            <form action="{{ route('admin.comandas.remove-item', [$comanda, $item]) }}" method="POST"
                                onsubmit="return confirm('Remover este item?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:text-red-700 text-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="{{ $comanda->isOpen() ? 5 : 4 }}" class="text-center text-gray-400 py-8">Nenhum item adicionado</td></tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Total e fechar --}}
            <div class="px-6 py-4 bg-gray-50 border-t">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total da Comanda</p>
                        <p class="text-3xl font-bold text-amber-700">R$ {{ number_format($comanda->total, 2, ',', '.') }}</p>
                    </div>
                    @if($comanda->isOpen() && $comanda->items->where('status','!=','cancelado')->count() > 0)
                    <button onclick="document.getElementById('modal-close').classList.remove('hidden')"
                        class="bg-green-600 hover:bg-green-700 text-white font-bold px-6 py-3 rounded-xl text-lg transition-colors">
                        <i class="fas fa-check mr-2"></i>Fechar Conta
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Fechar Conta --}}
<div id="modal-close" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="px-6 py-5 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 text-lg">Fechar Conta — {{ $comanda->number }}</h3>
            <button onclick="document.getElementById('modal-close').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('admin.comandas.close', $comanda) }}" method="POST" class="p-6 space-y-4">
            @csrf @method('PATCH')

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-center">
                <p class="text-amber-700 text-sm">Total a pagar</p>
                <p class="text-4xl font-bold text-amber-800" id="final-total">R$ {{ number_format($comanda->total, 2, ',', '.') }}</p>
            </div>

            <div>
                <label class="form-label">Desconto (R$)</label>
                <input type="number" name="discount" id="discount-input" value="0" step="0.01" min="0" class="form-input"
                    oninput="calcChange()">
            </div>

            <div>
                <label class="form-label">Forma de Pagamento *</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach(['dinheiro' => ['icon' => 'fa-money-bill', 'label' => 'Dinheiro'], 'pix' => ['icon' => 'fa-qrcode', 'label' => 'PIX'], 'cartao_credito' => ['icon' => 'fa-credit-card', 'label' => 'Crédito'], 'cartao_debito' => ['icon' => 'fa-credit-card', 'label' => 'Débito']] as $method => $info)
                    <label class="payment-option flex items-center gap-2 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-amber-400 transition-colors has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50">
                        <input type="radio" name="payment_method" value="{{ $method }}" required class="hidden" onchange="toggleCash('{{ $method }}')">
                        <i class="fas {{ $info['icon'] }} text-amber-600 w-5"></i>
                        <span class="text-sm font-medium text-gray-700">{{ $info['label'] }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div id="cash-received-wrapper" class="hidden">
                <label class="form-label">Valor Recebido (R$)</label>
                <input type="number" name="cash_received" id="cash-input" step="0.01" min="0" class="form-input text-lg"
                    placeholder="0.00" oninput="calcChange()">
                <div id="change-display" class="mt-2 p-3 bg-green-50 border border-green-200 rounded-lg hidden">
                    <p class="text-green-800 font-semibold text-center">Troco: <span id="change-amount">R$ 0,00</span></p>
                </div>
            </div>

            <div>
                <label class="form-label">Observações</label>
                <input type="text" name="notes" class="form-input" placeholder="Opcional">
            </div>

            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl text-lg transition-colors">
                <i class="fas fa-check mr-2"></i>Confirmar Pagamento
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
const totalComanda = {{ $comanda->total }};
let selectedPrice = 0;
let selectedType = 'unitario';

function selectProduct(radio) {
    selectedPrice = parseFloat(radio.dataset.price);
    selectedType = radio.dataset.type;
    document.getElementById('unit-label').textContent = selectedType === 'kg' ? 'kg' : 'un';
    document.getElementById('quantity').step = selectedType === 'kg' ? '0.001' : '1';
    document.getElementById('quantity').value = selectedType === 'kg' ? '0.500' : '1';
    calcItemTotal();
}

function calcItemTotal() {
    const qty = parseFloat(document.getElementById('quantity').value) || 0;
    const total = (selectedPrice * qty).toFixed(2);
    document.getElementById('total-preview').textContent = selectedPrice > 0 ? `Total: R$ ${total.replace('.', ',')}` : '';
}

document.getElementById('quantity')?.addEventListener('input', calcItemTotal);

function filterProducts(search) {
    const items = document.querySelectorAll('.product-item');
    items.forEach(item => {
        const match = item.dataset.name.includes(search.toLowerCase());
        item.style.display = match ? '' : 'none';
    });
}

function toggleCash(method) {
    const wrapper = document.getElementById('cash-received-wrapper');
    wrapper.classList.toggle('hidden', method !== 'dinheiro');
    calcChange();
}

function calcChange() {
    const discount = parseFloat(document.getElementById('discount-input').value) || 0;
    const paid = parseFloat(document.getElementById('cash-input')?.value) || 0;
    const final = Math.max(0, totalComanda - discount);
    document.getElementById('final-total').textContent = `R$ ${final.toFixed(2).replace('.', ',')}`;

    const change = paid - final;
    const changeEl = document.getElementById('change-display');
    if (paid > 0 && change >= 0) {
        document.getElementById('change-amount').textContent = `R$ ${change.toFixed(2).replace('.', ',')}`;
        changeEl?.classList.remove('hidden');
    } else {
        changeEl?.classList.add('hidden');
    }
}
</script>
@endpush
@endsection
