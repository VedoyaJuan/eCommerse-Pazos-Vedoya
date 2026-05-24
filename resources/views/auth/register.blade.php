<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse — Tic-Tac Store</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-md">

        {{-- Brand --}}
        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold text-white tracking-widest uppercase">Tic-Tac Store</h1>
            <p class="mt-2 text-slate-400 text-sm font-light">Creá tu cuenta de vendedor</p>
        </div>

        {{-- Card --}}
        <div class="bg-slate-800 rounded-2xl border border-slate-700 shadow-2xl p-8">

            <h2 class="text-lg font-medium text-white mb-6">Registro</h2>

            <form method="POST" action="{{ route('register') }}" novalidate>
                @csrf

                {{-- Name --}}
                <div class="mb-5">
                    <label for="name" class="block text-sm font-medium text-slate-300 mb-1.5">
                        Nombre completo
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        autocomplete="name"
                        class="w-full px-4 py-2.5 bg-slate-900 border {{ $errors->has('name') ? 'border-red-500' : 'border-slate-600' }} text-white text-sm rounded-lg placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent transition"
                        placeholder="Tu nombre"
                    >
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-slate-300 mb-1.5">
                        Correo electrónico
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                        class="w-full px-4 py-2.5 bg-slate-900 border {{ $errors->has('email') ? 'border-red-500' : 'border-slate-600' }} text-white text-sm rounded-lg placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent transition"
                        placeholder="tu@email.com"
                    >
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-5">
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-1.5">
                        Contraseña
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        class="w-full px-4 py-2.5 bg-slate-900 border {{ $errors->has('password') ? 'border-red-500' : 'border-slate-600' }} text-white text-sm rounded-lg placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent transition"
                        placeholder="Mínimo 8 caracteres"
                    >
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-1.5">
                        Confirmar contraseña
                    </label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        class="w-full px-4 py-2.5 bg-slate-900 border border-slate-600 text-white text-sm rounded-lg placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent transition"
                        placeholder="Repetí tu contraseña"
                    >
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full py-2.5 bg-white text-slate-900 font-semibold text-sm rounded-lg hover:bg-slate-100 active:bg-slate-200 transition-colors"
                >
                    Registrarme
                </button>

            </form>
        </div>

        {{-- Back to login --}}
        <p class="mt-6 text-center text-sm text-slate-400">
            ¿Ya tenés cuenta?
            <a href="{{ route('login') }}" class="text-white hover:underline font-medium">Iniciá sesión</a>
        </p>

    </div>

</body>
</html>
