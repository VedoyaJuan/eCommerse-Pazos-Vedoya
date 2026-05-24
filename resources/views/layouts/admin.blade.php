<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Venta de Relojes</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased h-screen flex overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col hidden md:flex">
        <div class="h-16 flex items-center justify-center border-b border-slate-800">
            <h1 class="text-xl font-semibold text-white tracking-widest uppercase">Tic-Tac Store</h1>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="{{ route('products.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('products.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 hover:text-white transition-colors' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Productos
            </a>
            <a href="{{ route('orders.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('orders.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 hover:text-white transition-colors' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                Pedidos
            </a>
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('users.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('users.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 hover:text-white transition-colors' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Usuarios
                @php $pending = \App\Models\User::where('role','vendedor')->where('status','pending')->count(); @endphp
                @if($pending > 0)
                    <span class="ml-auto inline-flex items-center justify-center w-5 h-5 rounded-full bg-yellow-500 text-white text-[10px] font-bold">{{ $pending }}</span>
                @endif
            </a>
            @endif
            <a href="/" class="flex items-center px-4 py-3 rounded-lg hover:bg-slate-800 hover:text-white transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Ir a la Tienda
            </a>
        </nav>

        {{-- User info + logout --}}
        <div class="px-4 py-4 border-t border-slate-800">
            <p class="text-xs text-slate-500 mb-1 truncate">{{ Auth::user()->name }}</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center w-full px-4 py-2.5 rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white transition-colors text-sm">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-hidden">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center px-6 justify-between shadow-sm">
            <h2 class="text-xl font-medium text-gray-800">@yield('title', 'Dashboard')</h2>
        </header>
        <div class="flex-1 overflow-y-auto p-6">
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-md">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-md">
                    {{ session('error') }}
                </div>
            @endif
            
            @yield('content')
        </div>
    </main>

</body>
</html>
