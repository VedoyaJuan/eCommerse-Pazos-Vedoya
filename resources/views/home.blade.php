@extends('layouts.store')

@section('title', 'Catálogo | Tic-Tac Store')

@section('content')
<div class="bg-zinc-900 py-20 text-center mb-12">
    <h1 class="text-4xl md:text-5xl font-light text-white tracking-widest mb-4 uppercase">
        Explora la <span class="font-semibold text-amber-500">Colección</span>
    </h1>
    <p class="text-zinc-400 max-w-2xl mx-auto font-light">
        Descubre nuestra selección de relojes de alta gama, diseñados para acompañarte en cada segundo de tu vida con precisión y elegancia.
    </p>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-8 gap-y-12">
        @forelse($products as $product)
        <div class="group flex flex-col bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-zinc-100">
            <a href="{{ route('store.show', $product) }}" class="relative aspect-square overflow-hidden bg-zinc-100 block">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out">
                @else
                    <div class="w-full h-full flex items-center justify-center text-zinc-300">
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                @endif
                <div class="absolute top-4 left-4">
                    <span class="bg-white/90 backdrop-blur-sm text-zinc-900 text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wide">
                        {{ $product->brand ?? 'Luxury' }}
                    </span>
                </div>
            </a>
            <div class="p-6 flex flex-col flex-grow">
                <a href="{{ route('store.show', $product) }}" class="block mb-2 group-hover:text-amber-600 transition-colors">
                    <h3 class="text-lg font-medium text-zinc-900 leading-tight">{{ $product->name }}</h3>
                </a>
                <p class="text-zinc-500 text-sm line-clamp-2 mb-4 font-light">{{ $product->description }}</p>
                <div class="mt-auto flex items-center justify-between">
                    <span class="text-xl font-semibold text-zinc-900">${{ number_format($product->price, 2) }}</span>
                    <a href="{{ route('store.show', $product) }}" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-zinc-100 text-zinc-600 group-hover:bg-amber-500 group-hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center text-zinc-500">
            No hay relojes disponibles en este momento.
        </div>
        @endforelse
    </div>

    @if($products->hasPages())
    <div class="mt-16 flex justify-center">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection
