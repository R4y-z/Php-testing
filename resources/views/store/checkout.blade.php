@extends('layouts.store')
@section('title', 'Finalizar Pedido')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">
        <i class="fas fa-check-circle mr-2 text-amber-600"></i>Finalizar Pedido
    </h1>

    <form action="{{ route('store.checkout.store') }}" method="POST" class="space-y-5">
        @csrf

        {{-- Identificação --}}
        <div class="card space-y-4">
            <h2 class="font-semibold text-gray-800">Seus dados</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Nome *</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" required
                        class="form-input" placeholder="Seu nome completo">
                    @error('customer_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Telefone / WhatsApp *</label>
                    <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" required
                        class="form-input" placeholder="(79) 99999-9999">
                    @error('customer_phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Tipo de pedido --}}
        <div class="card space-y-3">
            <h2 class="font-semibold text-gray-800">Como quer receber?</h2>
            <div class="grid grid-cols-2 gap-3">
                <label class="flex flex-col items-center gap-2 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-amber-400 has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50 transition-all">
                    <input type="radio" name="order_type" value="delivery" required class="hidden" onchange="toggleDelivery('delivery')" {{ old('order_type') === 'delivery' ? 'checked' : '' }}>
                    <i class="fas fa-motorcycle text-3xl text-amber-600"></i>
                    <span class="font-semibold text-gray-700">Delivery</span>
                    <span class="text-xs text-gray-400">Taxa: R$ {{ number_format($delivery['delivery_fee'] ?? 5, 2, ',', '.') }}</span>
                </label>
                <label class="flex flex-col items-center gap-2 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-amber-400 has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50 transition-all">
                    <input type="radio" name="order_type" value="retirada" required class="hidden" onchange="toggleDelivery('retirada')" {{ old('order_type') === 'retirada' ? 'checked' : '' }}>
                    <i class="fas fa-store text-3xl text-amber-600"></i>
                    <span class="font-semibold text-gray-700">Retirada</span>
                    <span class="text-xs text-gray-400">Sem taxa</span>
                </label>
            </div>
            @error('order_type')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
        </div>

        {{-- Endereço de entrega --}}
        <div id="delivery-address" class="card space-y-3 {{ old('order_type') === 'delivery' ? '' : 'hidden' }}">
            <h2 class="font-semibold text-gray-800">Endereço de entrega</h2>
            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <label class="form-label">Rua *</label>
                    <input type="text" name="address_street" value="{{ old('address_street') }}" class="form-input" placeholder="Rua, Av...">
                    @error('address_street')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Número *</label>
                    <input type="text" name="address_number" value="{{ old('address_number') }}" class="form-input">
                    @error('address_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="form-label">Bairro *</label>
                <input type="text" name="address_neighborhood" value="{{ old('address_neighborhood') }}" class="form-input">
                @error('address_neighborhood')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Referência</label>
                <input type="text" name="address_reference" value="{{ old('address_reference') }}" class="form-input" placeholder="Próximo a...">
            </div>
        </div>

        {{-- Pagamento --}}
        <div class="card space-y-3">
            <h2 class="font-semibold text-gray-800">Forma de pagamento</h2>
            <div class="grid grid-cols-2 gap-3">
                @php
                    $methods = ['dinheiro' => ['icon' => 'fa-money-bill-wave', 'label' => 'Dinheiro'], 'pix' => ['icon' => 'fa-qrcode', 'label' => 'PIX'], 'cartao_credito' => ['icon' => 'fa-credit-card', 'label' => 'Cartão Crédito'], 'cartao_debito' => ['icon' => 'fa-credit-card', 'label' => 'Cartão Débito']];
                @endphp
                @foreach($methods as $value => $info)
                <label class="flex items-center gap-2 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-amber-400 has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50 transition-all">
                    <input type="radio" name="payment_method" value="{{ $value }}" required class="hidden" {{ old('payment_method') === $value ? 'checked' : '' }}>
                    <i class="fas {{ $info['icon'] }} text-amber-600 w-5"></i>
                    <span class="text-sm font-medium text-gray-700">{{ $info['label'] }}</span>
                </label>
                @endforeach
            </div>
            @error('payment_method')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
        </div>

        {{-- Observações --}}
        <div class="card">
            <label class="form-label">Observações</label>
            <textarea name="notes" rows="2" class="form-input" placeholder="Alguma observação especial?">{{ old('notes') }}</textarea>
        </div>

        {{-- Resumo --}}
        @php
            $subtotal = array_sum(array_column($cart, 'total'));
            $deliveryFee = (float)($delivery['delivery_fee'] ?? 5);
        @endphp
        <div class="card bg-amber-50 border-amber-200">
            <div class="space-y-2 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span>R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-gray-600" id="delivery-fee-row">
                    <span>Taxa de entrega</span>
                    <span id="delivery-fee-value">R$ {{ number_format($deliveryFee, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold text-lg text-gray-900 border-t pt-2">
                    <span>Total</span>
                    <span id="order-total">R$ {{ number_format($subtotal + $deliveryFee, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-4 rounded-xl text-lg transition-colors">
            <i class="fas fa-check mr-2"></i>Confirmar Pedido
        </button>
    </form>
</div>

@push('scripts')
<script>
const subtotal = {{ $subtotal }};
const deliveryFee = {{ $deliveryFee }};

function toggleDelivery(type) {
    const addressDiv = document.getElementById('delivery-address');
    const feeRow = document.getElementById('delivery-fee-row');
    const feeValue = document.getElementById('delivery-fee-value');
    const totalEl = document.getElementById('order-total');

    if (type === 'delivery') {
        addressDiv.classList.remove('hidden');
        feeRow.classList.remove('hidden');
        feeValue.textContent = `R$ ${deliveryFee.toFixed(2).replace('.', ',')}`;
        totalEl.textContent = `R$ ${(subtotal + deliveryFee).toFixed(2).replace('.', ',')}`;
    } else {
        addressDiv.classList.add('hidden');
        feeRow.classList.add('hidden');
        totalEl.textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
    }
}

// Initializar se já tiver seleção
const selected = document.querySelector('input[name="order_type"]:checked');
if (selected) toggleDelivery(selected.value);
</script>
@endpush
@endsection
