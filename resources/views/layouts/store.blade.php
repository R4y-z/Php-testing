<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cardápio') — Churrascaria Nordestina</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 500: '#d4841e', 600: '#b86516', 700: '#8f4b13' },
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @livewireStyles
</head>
<body class="bg-gray-50 font-sans">

{{-- Header --}}
<header class="bg-gradient-to-r from-amber-900 to-amber-800 text-white sticky top-0 z-50 shadow-lg">
    <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-amber-500 rounded-full flex items-center justify-center">
                <i class="fas fa-fire text-white"></i>
            </div>
            <div>
                <h1 class="font-bold text-lg leading-tight">Churrascaria Nordestina</h1>
                <p class="text-amber-300 text-xs">Canindé de São Francisco, SE</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            {{-- Carrinho --}}
            <a href="{{ route('store.cart') }}" class="relative bg-amber-700 hover:bg-amber-600 px-4 py-2 rounded-full flex items-center gap-2 transition-colors">
                <i class="fas fa-shopping-cart"></i>
                <span class="hidden sm:inline text-sm font-medium">Carrinho</span>
                <span id="cart-count" class="bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold">
                    {{ count(session('cart', [])) }}
                </span>
            </a>
            {{-- Login --}}
            @guest
            <a href="{{ route('login') }}" class="text-amber-300 hover:text-white text-sm">
                <i class="fas fa-user mr-1"></i>Entrar
            </a>
            @else
            <a href="{{ auth()->user()->canAccessAdmin() ? '/admin/dashboard' : '#' }}" class="text-amber-300 hover:text-white text-sm">
                <i class="fas fa-user-circle mr-1"></i>{{ auth()->user()->name }}
            </a>
            @endguest
        </div>
    </div>
</header>

{{-- Mensagens --}}
@if(session('success'))
<div class="bg-green-100 border-b border-green-300 text-green-800 px-4 py-3 text-sm text-center">
    <i class="fas fa-check-circle mr-1"></i>{{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="bg-red-100 border-b border-red-300 text-red-800 px-4 py-3 text-sm text-center">
    <i class="fas fa-exclamation-circle mr-1"></i>{{ session('error') }}
</div>
@endif

{{-- Conteúdo --}}
<main>
    @yield('content')
</main>

{{-- Footer --}}
<footer class="bg-amber-900 text-amber-200 text-center py-6 mt-12 text-sm">
    <p class="font-semibold text-white">Churrascaria Nordestina</p>
    <p class="mt-1">Canindé de São Francisco, SE</p>
    <p class="mt-1">
        <i class="fab fa-whatsapp mr-1 text-green-400"></i>
        <a href="https://wa.me/{{ preg_replace('/\D/', '', \App\Models\Setting::get('restaurant_whatsapp', '')) }}" target="_blank" class="hover:text-white">
            {{ \App\Models\Setting::get('restaurant_phone', '(79) 99999-9999') }}
        </a>
    </p>
</footer>

<style>
    .btn-primary { @apply bg-amber-600 hover:bg-amber-700 text-white font-semibold px-5 py-2.5 rounded-lg transition-colors; }
    .btn-secondary { @apply bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 font-medium px-5 py-2.5 rounded-lg transition-colors; }
    .card { @apply bg-white rounded-xl shadow-sm border border-gray-100; }
    .form-input { @apply w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-amber-400 outline-none; }
    .form-label { @apply block text-sm font-medium text-gray-700 mb-1; }
</style>

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('cart-updated', (event) => {
            const el = document.getElementById('cart-count');
            if (el) el.textContent = event.count;
        });
    });
</script>

@livewireScripts
@stack('scripts')
</body>
</html>
