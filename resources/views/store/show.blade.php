@extends('layouts.store')

@section('title', $product->name . ' | Tic-Tac Store')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Breadcrumbs -->
    <nav class="flex mb-8 text-sm font-medium text-zinc-500">
        <a href="{{ route('home') }}" class="hover:text-amber-600 transition-colors">Catálogo</a>
        <span class="mx-2">/</span>
        <span class="text-zinc-900">{{ $product->name }}</span>
    </nav>

    <div class="flex flex-col md:flex-row gap-12 lg:gap-20">
        <!-- Imagen -->
        <div class="w-full md:w-1/2 lg:w-3/5">
            <div class="aspect-square rounded-3xl overflow-hidden bg-zinc-100 shadow-sm border border-zinc-100 relative">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover object-center">
                @else
                    <div class="w-full h-full flex items-center justify-center text-zinc-300">
                        <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                @endif
                <div class="absolute top-6 left-6">
                    <span class="bg-white/90 backdrop-blur-md text-zinc-900 text-sm font-bold px-4 py-2 rounded-full uppercase tracking-wider shadow-sm">
                        {{ $product->brand ?? 'Luxury Collection' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Detalles -->
        <div class="w-full md:w-1/2 lg:w-2/5 flex flex-col">
            <h1 class="text-3xl lg:text-4xl font-semibold text-zinc-900 mb-2">{{ $product->name }}</h1>
            <div class="text-3xl font-light text-zinc-900 mb-8">${{ number_format($product->price, 2) }}</div>
            
            <div class="prose prose-zinc mb-10 text-zinc-600 font-light leading-relaxed">
                <p>{{ $product->description }}</p>
            </div>

            <div class="border-t border-zinc-200 pt-8 mb-8 space-y-4 text-sm">
                <div class="flex justify-between items-center">
                    <span class="text-zinc-500 uppercase tracking-wider">Disponibilidad</span>
                    <span class="{{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }} font-medium bg-green-50 px-3 py-1 rounded-md">
                        {{ $product->stock > 0 ? $product->stock . ' en stock' : 'Agotado' }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-zinc-500 uppercase tracking-wider">Envío</span>
                    <span class="text-zinc-900 font-medium">Gratis a todo el país</span>
                </div>
            </div>

            <!-- Acciones -->
            <form class="mt-auto">
                <div class="flex space-x-4 mb-6">
                    <div class="w-1/3">
                        <label for="quantity" class="sr-only">Cantidad</label>
                        <select id="quantity" name="quantity" class="w-full h-14 rounded-xl border-zinc-300 text-zinc-700 bg-white shadow-sm focus:border-amber-500 focus:ring-amber-500 appearance-none px-4 outline-none" {{ $product->stock == 0 ? 'disabled' : '' }}>
                            @for ($i = 1; $i <= min(5, max(1, $product->stock)); $i++)
                                <option value="{{ $i }}">{{ $i }} un.</option>
                            @endfor
                        </select>
                    </div>
                    <button type="button" class="w-2/3 h-14 flex items-center justify-center bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-medium tracking-wide uppercase transition-all duration-300 shadow-md hover:shadow-xl disabled:bg-zinc-300 disabled:cursor-not-allowed" {{ $product->stock == 0 ? 'disabled' : '' }}>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Añadir al carrito
                    </button>
                </div>
            </form>
            
            <div class="bg-zinc-50 rounded-xl p-6 border border-zinc-100 flex items-start space-x-4">
                <svg class="w-8 h-8 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                <div>
                    <h4 class="text-sm font-semibold text-zinc-900">Garantía Internacional</h4>
                    <p class="text-xs text-zinc-500 mt-1">Todos nuestros relojes incluyen 2 años de garantía oficial internacional directa con el fabricante.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
