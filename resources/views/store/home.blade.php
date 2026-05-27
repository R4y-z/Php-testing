@extends('layouts.store')
@section('title', 'Cardápio')

@section('content')

{{-- Banner hero --}}
<div class="bg-gradient-to-r from-amber-900 to-amber-700 text-white">
    <div class="max-w-5xl mx-auto px-4 py-10 text-center">
        <h2 class="text-3xl sm:text-4xl font-bold mb-2">Cardápio Online</h2>
        <p class="text-amber-300 text-lg">Churrascaria Nordestina — Canindé de São Francisco, SE</p>
        @if($delivery['delivery_enabled'] ?? false)
        <p class="text-amber-200 text-sm mt-3">
            <i class="fas fa-motorcycle mr-1"></i>
            Delivery disponível • Taxa: R$ {{ number_format($delivery['delivery_fee'] ?? 5, 2, ',', '.') }}
            • Pedido mínimo: R$ {{ number_format($delivery['min_order'] ?? 20, 2, ',', '.') }}
        </p>
        @endif
    </div>
</div>

{{-- Navegação por categorias --}}
<div class="bg-white border-b border-gray-200 sticky top-16 z-40">
    <div class="max-w-5xl mx-auto px-4">
        <div class="flex gap-1 overflow-x-auto py-3 scrollbar-hide">
            @foreach($categories as $category)
            <a href="#cat-{{ $category->id }}"
                class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium text-gray-600 hover:bg-amber-100 hover:text-amber-800 transition-colors whitespace-nowrap">
                {{ $category->name }}
            </a>
            @endforeach
        </div>
    </div>
</div>

{{-- Cardápio --}}
<div class="max-w-5xl mx-auto px-4 py-8">

    @foreach($categories as $category)
    <div id="cat-{{ $category->id }}" class="mb-10">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-1 h-8 bg-amber-500 rounded-full"></div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $category->name }}</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($category->activeProducts as $product)
            <div class="card overflow-hidden hover:shadow-md transition-shadow group">
                {{-- Imagem --}}
                <div class="h-44 overflow-hidden bg-gray-100">
                    @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        alt="{{ $product->name }}">
                    @else
                    <div class="w-full h-full flex items-center justify-center">
                        <i class="fas fa-utensils text-4xl text-gray-300"></i>
                    </div>
                    @endif
                </div>

                {{-- Informações --}}
                <div class="p-4">
                    <div class="flex items-start justify-between mb-1">
                        <h3 class="font-semibold text-gray-900 text-base leading-tight">{{ $product->name }}</h3>
                        @if($product->type === 'kg')
                        <span class="badge bg-purple-100 text-purple-700 ml-2 flex-shrink-0">KG</span>
                        @endif
                    </div>

                    @if($product->description)
                    <p class="text-sm text-gray-500 mb-3 line-clamp-2">{{ $product->description }}</p>
                    @endif

                    <div class="flex items-center justify-between mt-3">
                        <div>
                            <span class="text-2xl font-bold text-amber-700">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                            @if($product->type === 'kg')
                            <span class="text-xs text-gray-400">/kg</span>
                            @endif
                        </div>

                        <button
                            onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, '{{ $product->type }}')"
                            class="bg-amber-600 hover:bg-amber-700 text-white font-semibold px-4 py-2 rounded-xl text-sm transition-colors flex items-center gap-1.5">
                            <i class="fas fa-plus text-xs"></i>
                            @if($product->type === 'kg') Pedir @else Adicionar @endif
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    @if($categories->isEmpty())
    <div class="text-center py-20">
        <i class="fas fa-utensils text-6xl text-gray-200 mb-4 block"></i>
        <p class="text-gray-400 text-xl">Cardápio sendo atualizado...</p>
        <p class="text-gray-400 mt-2">Entre em contato pelo WhatsApp</p>
    </div>
    @endif
</div>

{{-- Botão flutuante do carrinho --}}
<div id="float-cart" class="hidden fixed bottom-6 right-6 z-50">
    <a href="{{ route('store.cart') }}"
        class="bg-amber-600 hover:bg-amber-700 text-white rounded-full px-6 py-4 shadow-2xl flex items-center gap-3 transition-all">
        <i class="fas fa-shopping-cart text-xl"></i>
        <span class="font-bold text-lg" id="float-cart-count">0</span>
        <span class="font-medium">Ver carrinho</span>
    </a>
</div>

{{-- Modal KG --}}
<div id="modal-kg" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
        <div class="px-6 py-5 border-b flex items-center justify-between">
            <h3 class="font-semibold text-lg" id="kg-product-name">Produto por KG</h3>
            <button onclick="document.getElementById('modal-kg').classList.add('hidden')" class="text-gray-400">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="form-label">Peso estimado (kg)</label>
                <div class="flex gap-2 items-center">
                    <input type="number" id="kg-quantity" value="0.5" step="0.1" min="0.1" max="10"
                        class="form-input text-2xl font-bold text-center" oninput="updateKgTotal()">
                    <span class="text-gray-500 font-medium">kg</span>
                </div>
                <div class="flex gap-2 mt-2">
                    @foreach([0.3, 0.5, 0.75, 1.0] as $preset)
                    <button onclick="document.getElementById('kg-quantity').value='{{ $preset }}'; updateKgTotal()"
                        class="flex-1 py-1.5 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-amber-50 hover:border-amber-300">
                        {{ number_format($preset, 1, ',', '.') }}
                    </button>
                    @endforeach
                </div>
            </div>
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-center">
                <p class="text-amber-600 text-sm">Estimativa:</p>
                <p class="text-3xl font-bold text-amber-800" id="kg-total">R$ 0,00</p>
            </div>
            <p class="text-xs text-gray-400 text-center">O peso real será medido na balança. Valor ajustado na comanda.</p>
            <button onclick="confirmKgAdd()" class="btn-primary w-full py-3 text-base">
                <i class="fas fa-plus mr-2"></i>Adicionar ao Carrinho
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let kgProductId = null;
let kgProductName = '';
let kgProductPrice = 0;

function addToCart(productId, name, price, type) {
    if (type === 'kg') {
        kgProductId = productId;
        kgProductName = name;
        kgProductPrice = price;
        document.getElementById('kg-product-name').textContent = name;
        document.getElementById('kg-quantity').value = '0.5';
        updateKgTotal();
        document.getElementById('modal-kg').classList.remove('hidden');
        return;
    }

    fetch('/loja/carrinho/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ product_id: productId, quantity: 1 })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            updateCartUI(data.count);
            showToast(`${name} adicionado!`);
        }
    });
}

function confirmKgAdd() {
    const qty = parseFloat(document.getElementById('kg-quantity').value);
    if (!qty || qty <= 0) return;

    fetch('/loja/carrinho/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ product_id: kgProductId, quantity: qty })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('modal-kg').classList.add('hidden');
            updateCartUI(data.count);
            showToast(`${kgProductName} adicionado!`);
        }
    });
}

function updateKgTotal() {
    const qty = parseFloat(document.getElementById('kg-quantity').value) || 0;
    const total = (qty * kgProductPrice).toFixed(2);
    document.getElementById('kg-total').textContent = 'R$ ' + total.replace('.', ',');
}

function updateCartUI(count) {
    document.getElementById('cart-count').textContent = count;
    const floatCart = document.getElementById('float-cart');
    document.getElementById('float-cart-count').textContent = count;
    if (count > 0) {
        floatCart.classList.remove('hidden');
    }
}

function showToast(msg) {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-24 right-6 bg-green-600 text-white px-5 py-3 rounded-xl shadow-lg text-sm font-medium z-50 transition-all';
    toast.innerHTML = `<i class="fas fa-check mr-2"></i>${msg}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 2500);
}

// Inicializar botão flutuante
const count = parseInt(document.getElementById('cart-count').textContent);
if (count > 0) {
    document.getElementById('float-cart').classList.remove('hidden');
    document.getElementById('float-cart-count').textContent = count;
}
</script>
@endpush
@endsection
