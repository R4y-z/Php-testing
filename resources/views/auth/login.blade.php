@extends('layouts.auth')

@section('content')
<div class="w-full max-w-md">
    {{-- Card --}}
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-amber-800 to-amber-700 px-8 py-8 text-center">
            <div class="w-16 h-16 bg-amber-500 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-fire text-white text-2xl"></i>
            </div>
            <h1 class="text-white font-bold text-2xl">Churrascaria Nordestina</h1>
            <p class="text-amber-300 text-sm mt-1">Canindé de São Francisco, SE</p>
        </div>

        {{-- Form --}}
        <div class="px-8 py-8">
            <h2 class="text-gray-800 font-semibold text-lg mb-6 text-center">Entrar no Sistema</h2>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-envelope mr-1 text-gray-400"></i> E-mail
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none @error('email') border-red-400 @enderror"
                        placeholder="seu@email.com">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-lock mr-1 text-gray-400"></i> Senha
                    </label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none pr-10"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePassword()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i id="eye-icon" class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-amber-500">
                        Lembrar-me
                    </label>
                </div>

                <button type="submit"
                    class="w-full bg-amber-600 hover:bg-amber-700 text-white font-semibold py-3 rounded-lg transition-colors duration-150 text-sm">
                    <i class="fas fa-sign-in-alt mr-2"></i> Entrar
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('store.index') }}" class="text-amber-700 hover:text-amber-800 text-sm">
                    <i class="fas fa-store mr-1"></i> Ver Cardápio Online
                </a>
            </div>
        </div>
    </div>

    <p class="text-center text-amber-400 text-xs mt-4">
        © {{ date('Y') }} Churrascaria Nordestina — Sistema PDV
    </p>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('eye-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>
@endsection
