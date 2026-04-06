<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tic-Tac Store | Bienvenida</title>

    <!-- Tailwind CSS (CDN para asegurar los estilos si no está corriendo npm run dev) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        h1, h2 {
            font-family: 'Playfair Display', serif;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col items-center justify-center p-6 bg-[url('https://images.unsplash.com/photo-1548169874-53ce86f44026?auto=format&fit=crop&q=80&w=2000')] bg-cover bg-center">
    
    <!-- Overlay oscuro -->
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

    <div class="relative z-10 w-full max-w-3xl bg-white/10 backdrop-blur-md border border-white/20 p-10 md:p-14 rounded-2xl shadow-2xl text-center text-white">
        
        <!-- Logo / Icono -->
        <div class="flex justify-center mb-6">
            <svg class="w-16 h-16 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>

        <!-- Título Principal -->
        <h1 class="text-4xl md:text-6xl font-bold mb-4 tracking-wide text-white drop-shadow-md">
            Tic-Tac <span class="text-amber-400">Store</span>
        </h1>
        <p class="text-lg md:text-xl font-light tracking-widest uppercase mb-10 text-slate-300">
            Relojería Online de Alta Gama
        </p>

        <div class="w-24 h-1 bg-amber-400 mx-auto mb-10 rounded-full"></div>

        <!-- Información Académica -->
        <div class="bg-black/30 md:bg-transparent rounded-xl p-6 md:p-0">
            <h2 class="text-2xl font-semibold mb-2 text-amber-300">Proyecto Universitario</h2>
            <p class="text-slate-200 mb-6 font-medium">Materia: Aplicaciones Web <br> <span class="text-sm font-normal text-slate-300">Universidad Nacional de la Patagonia San Juan Bosco (UNPSJB)</span></p>
            
            <h3 class="text-lg font-semibold text-white mb-2 uppercase tracking-wide">Integrantes</h3>
            <ul class="flex flex-col md:flex-row justify-center gap-2 md:gap-8 text-slate-200 font-medium text-lg">
                <li class="flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Vedoya Juan Pablo
                </li>
                <li class="flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Pazos Sebastian Luis
                </li>
            </ul>
        </div>

        <!-- Botones de Acción (Opcional) -->
        <div class="mt-12 flex justify-center gap-4">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-8 py-3 bg-amber-500 hover:bg-amber-600 transition-colors text-slate-900 font-semibold rounded-lg shadow-lg">Ir al Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-8 py-3 border-2 border-amber-400 text-amber-400 hover:bg-amber-400 hover:text-slate-900 transition-colors font-semibold rounded-lg">Iniciar Sesión</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-8 py-3 bg-white/10 hover:bg-white/20 transition-colors border border-white/30 font-semibold rounded-lg">Registrarse</a>
                    @endif
                @endauth
            @endif
        </div>
    </div>
    
    <div class="relative z-10 mt-8 text-sm text-slate-400 font-light text-center">
        &copy; 2026 Tic-Tac Store. Todos los derechos reservados.
    </div>

</body>
</html>
