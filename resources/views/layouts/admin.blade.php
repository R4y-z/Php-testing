<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Painel') — Churrascaria Nordestina</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50:  '#fdf8f0',
                            100: '#faefd8',
                            200: '#f5d9a1',
                            300: '#efc169',
                            400: '#e8a43a',
                            500: '#d4841e',
                            600: '#b86516',
                            700: '#8f4b13',
                            800: '#6b3510',
                            900: '#4a230b',
                        },
                        sidebar: '#2d1a0e',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @livewireStyles
</head>
<body class="bg-gray-100 font-sans">

{{-- Sidebar --}}
<div class="flex h-screen overflow-hidden">
    <aside id="sidebar" class="w-64 bg-sidebar text-white flex flex-col flex-shrink-0 transition-all duration-300">
        {{-- Logo --}}
        <div class="px-6 py-5 border-b border-yellow-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-brand-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-fire text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="font-bold text-sm leading-tight text-brand-300">Churrascaria</h1>
                    <h2 class="text-xs text-gray-400">Nordestina</h2>
                </div>
            </div>
        </div>

        {{-- Usuário --}}
        <div class="px-6 py-3 border-b border-yellow-900 text-sm">
            <p class="text-brand-300 font-medium">{{ auth()->user()->name }}</p>
            <p class="text-gray-400 text-xs">{{ auth()->user()->role?->name }}</p>
        </div>

        {{-- Navegação --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 text-sm">
            @php $can = fn($roles) => in_array(auth()->user()->role?->slug, $roles); @endphp

            @if($can(['admin','garcom','caixa','cozinha','delivery']))
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line w-5"></i> Dashboard
            </a>
            @endif

            @if($can(['admin','garcom','caixa']))
            <div class="pt-2 pb-1">
                <p class="text-gray-500 text-xs uppercase tracking-wider px-3">Operações</p>
            </div>
            <a href="{{ route('admin.comandas.index') }}" class="nav-link {{ request()->routeIs('admin.comandas*') ? 'active' : '' }}">
                <i class="fas fa-receipt w-5"></i> Comandas
            </a>
            <a href="{{ route('admin.tables.index') }}" class="nav-link {{ request()->routeIs('admin.tables*') ? 'active' : '' }}">
                <i class="fas fa-chair w-5"></i> Mesas
            </a>
            @endif

            @if($can(['admin','caixa']))
            <a href="{{ route('admin.cash.index') }}" class="nav-link {{ request()->routeIs('admin.cash*') ? 'active' : '' }}">
                <i class="fas fa-cash-register w-5"></i> Caixa
            </a>
            @endif

            @if($can(['admin','garcom','caixa']))
            <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                <i class="fas fa-shopping-bag w-5"></i> Pedidos
            </a>
            @endif

            @if($can(['admin','cozinha']))
            <a href="{{ route('admin.kitchen.index') }}" class="nav-link {{ request()->routeIs('admin.kitchen*') ? 'active' : '' }}">
                <i class="fas fa-kitchen-set w-5"></i> Cozinha
            </a>
            @endif

            @if($can(['admin','delivery']))
            <a href="{{ route('admin.delivery.index') }}" class="nav-link {{ request()->routeIs('admin.delivery*') ? 'active' : '' }}">
                <i class="fas fa-motorcycle w-5"></i> Delivery
            </a>
            @endif

            @if($can(['admin']))
            <div class="pt-2 pb-1">
                <p class="text-gray-500 text-xs uppercase tracking-wider px-3">Cardápio</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                <i class="fas fa-utensils w-5"></i> Produtos
            </a>
            <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                <i class="fas fa-tags w-5"></i> Categorias
            </a>

            <div class="pt-2 pb-1">
                <p class="text-gray-500 text-xs uppercase tracking-wider px-3">Gestão</p>
            </div>
            <a href="{{ route('admin.customers.index') }}" class="nav-link {{ request()->routeIs('admin.customers*') ? 'active' : '' }}">
                <i class="fas fa-users w-5"></i> Clientes
            </a>
            <a href="{{ route('admin.stock.index') }}" class="nav-link {{ request()->routeIs('admin.stock*') ? 'active' : '' }}">
                <i class="fas fa-boxes-stacked w-5"></i> Estoque
            </a>
            <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar w-5"></i> Relatórios
            </a>
            <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                <i class="fas fa-cog w-5"></i> Configurações
            </a>
            @endif
        </nav>

        {{-- Logout --}}
        <div class="px-3 pb-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link w-full text-left text-red-400 hover:text-red-300">
                    <i class="fas fa-sign-out-alt w-5"></i> Sair
                </button>
            </form>
        </div>
    </aside>

    {{-- Conteúdo Principal --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Top Bar --}}
        <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button onclick="document.getElementById('sidebar').classList.toggle('w-16')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h2 class="text-gray-800 font-semibold text-lg">@yield('page-title', 'Painel')</h2>
            </div>
            <div class="flex items-center gap-3 text-sm text-gray-600">
                <span class="hidden sm:block">{{ now()->format('d/m/Y H:i') }}</span>
                <a href="{{ route('store.index') }}" target="_blank" class="text-brand-600 hover:text-brand-700">
                    <i class="fas fa-store mr-1"></i>Ver Loja
                </a>
            </div>
        </header>

        {{-- Alertas --}}
        <div class="px-6 pt-3">
            @if(session('success'))
                <div class="alert-success">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    <button onclick="this.parentElement.remove()" class="float-right font-bold">×</button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                    <button onclick="this.parentElement.remove()" class="float-right font-bold">×</button>
                </div>
            @endif
        </div>

        {{-- Main --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

<style>
    .nav-link {
        @apply flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-yellow-900 hover:text-white transition-colors duration-150;
    }
    .nav-link.active {
        @apply bg-brand-600 text-white;
    }
    .alert-success {
        @apply bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded-lg mb-3 text-sm;
    }
    .alert-error {
        @apply bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-lg mb-3 text-sm;
    }
    .btn-primary {
        @apply bg-brand-600 hover:bg-brand-700 text-white font-medium px-4 py-2 rounded-lg transition-colors;
    }
    .btn-secondary {
        @apply bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-4 py-2 rounded-lg transition-colors;
    }
    .btn-danger {
        @apply bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-lg transition-colors;
    }
    .card {
        @apply bg-white rounded-xl shadow-sm border border-gray-100 p-6;
    }
    .form-input {
        @apply w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-400 focus:border-brand-400 outline-none;
    }
    .form-label {
        @apply block text-sm font-medium text-gray-700 mb-1;
    }
    .badge {
        @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
    }
    table { @apply w-full text-sm; }
    thead th { @apply text-left px-4 py-3 text-gray-600 font-semibold bg-gray-50 border-b; }
    tbody td { @apply px-4 py-3 border-b border-gray-100; }
    tbody tr:hover { @apply bg-gray-50; }
</style>

@livewireScripts
@stack('scripts')
</body>
</html>
