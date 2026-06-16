@extends('layouts.store')

@section('title', '¡Gracias por tu compra! | Tic-Tac Store')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <!-- Card de Éxito Principal -->
    <div class="bg-white rounded-3xl border border-zinc-100 shadow-xl overflow-hidden mb-10">
        <!-- Banner Superior de Éxito -->
        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-6 py-12 text-center text-white relative">
            <div class="absolute inset-0 bg-black/10 mix-blend-overlay"></div>
            <!-- Círculo animado del Checkmark -->
            <div class="mx-auto w-20 h-20 bg-white/10 backdrop-blur-md rounded-full flex items-center justify-center border border-white/20 mb-4 shadow-lg">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold tracking-wide uppercase">¡Pedido Recibido!</h1>
            <p class="text-emerald-50/90 font-light mt-2 max-w-md mx-auto">Gracias por confiar en Tic-Tac Store. Hemos registrado tu pedido de manera exitosa.</p>
        </div>

        <div class="p-8 space-y-8">
            <!-- Detalles de Cabecera del Pedido -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 border-b border-zinc-100 pb-8 text-center sm:text-left">
                <div>
                    <span class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider">Número de Pedido</span>
                    <span class="text-lg font-bold text-zinc-900 mt-1">#{{ $order->id }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider">Fecha</span>
                    <span class="text-sm font-medium text-zinc-800 mt-1 block">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider">Estado</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-50 text-yellow-800 border border-yellow-100 mt-1">
                        Pendiente
                    </span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider">Método de Envío</span>
                    <span class="text-sm font-medium text-zinc-800 mt-1 block">
                        {{ $order->shipping_option === 'delivery' ? 'A Domicilio' : 'Retiro en Local' }}
                    </span>
                </div>
            </div>

            <!-- Datos del Comprador y Envío -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-b border-zinc-100 pb-8">
                <!-- Información Personal -->
                <div class="space-y-3">
                    <h3 class="text-sm font-bold text-zinc-400 uppercase tracking-wider">Datos del Comprador</h3>
                    <div class="bg-zinc-50 rounded-2xl p-5 border border-zinc-100 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-xs text-zinc-400 font-medium">Nombre:</span>
                            <span class="text-sm font-medium text-zinc-900">{{ $order->customer_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-zinc-400 font-medium">Email:</span>
                            <span class="text-sm font-medium text-zinc-900">{{ $order->customer_email }}</span>
                        </div>
                        @if($order->customer_phone)
                        <div class="flex justify-between">
                            <span class="text-xs text-zinc-400 font-medium">Teléfono:</span>
                            <span class="text-sm font-medium text-zinc-900">{{ $order->customer_phone }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Dirección de Entrega -->
                <div class="space-y-3">
                    <h3 class="text-sm font-bold text-zinc-400 uppercase tracking-wider">Detalles de Entrega</h3>
                    <div class="bg-zinc-50 rounded-2xl p-5 border border-zinc-100 h-full flex flex-col justify-center">
                        <span class="text-xs text-zinc-400 font-semibold uppercase tracking-wider mb-1">Dirección Registrada</span>
                        <p class="text-sm text-zinc-900 font-medium leading-relaxed">{{ $order->shipping_address }}</p>
                        @if($order->notes)
                        <span class="text-xs text-zinc-400 font-semibold uppercase tracking-wider mt-4 mb-1">Notas Adicionales</span>
                        <p class="text-sm text-zinc-600 font-light italic">"{{ $order->notes }}"</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Resumen de Productos -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-zinc-400 uppercase tracking-wider">Artículos Adquiridos</h3>
                <div class="border border-zinc-100 rounded-2xl overflow-hidden bg-white shadow-sm">
                    <ul class="divide-y divide-zinc-100">
                        @foreach($order->items as $item)
                        <li class="p-5 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                @if($item->product?->image_url)
                                    <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="w-12 h-12 rounded-xl object-cover border border-zinc-100">
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                @endif
                                <div>
                                    <span class="text-[10px] font-bold text-amber-500 uppercase tracking-wider">{{ $item->product?->brand?->name ?? 'Luxury' }}</span>
                                    <h4 class="text-sm font-medium text-zinc-900 mt-0.5">{{ $item->product?->name ?? 'Producto' }}</h4>
                                    <p class="text-xs text-zinc-400 mt-1 font-light">{{ $item->quantity }} ud. x ${{ number_format($item->unit_price, 2) }}</p>
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-zinc-900">
                                ${{ number_format($item->unit_price * $item->quantity, 2) }}
                            </span>
                        </li>
                        @endforeach
                    </ul>

                    <!-- Desglose de Totales -->
                    <div class="bg-zinc-50/50 p-5 border-t border-zinc-100 space-y-3">
                        <div class="flex justify-between text-sm text-zinc-500">
                            <span>Subtotal de artículos</span>
                            <span class="font-medium text-zinc-800">${{ number_format($order->total - $order->shipping_cost, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-zinc-500">
                            <span>Costo de envío</span>
                            <span class="font-medium text-zinc-800">${{ number_format($order->shipping_cost, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-base font-bold text-zinc-900 pt-3 border-t border-zinc-200">
                            <span>Total general</span>
                            <span>${{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Finales -->
    <div class="text-center">
        <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-8 h-12 bg-zinc-900 hover:bg-zinc-800 text-white rounded-xl font-medium uppercase tracking-wider transition-all shadow-md hover:shadow-xl">
            Volver al Catálogo
        </a>
    </div>
</div>
@endsection
