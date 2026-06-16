@extends('layouts.store')

@section('title', 'Mi Carrito | Tic-Tac Store')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-light text-zinc-900 tracking-wider uppercase mb-8">Mi Carrito</h1>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-100 text-green-800 px-4 py-3 rounded-xl text-sm flex items-center shadow-sm">
            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-100 text-red-800 px-4 py-3 rounded-xl text-sm flex items-center shadow-sm">
            <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    @if(count($products) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            <!-- Productos -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-zinc-100 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-zinc-100 bg-zinc-50/50">
                        <h2 class="text-lg font-semibold text-zinc-900">Artículos seleccionados</h2>
                    </div>
                    <ul class="divide-y divide-zinc-100">
                        @foreach($products as $product)
                            <li class="p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-4 flex-1">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-16 h-16 rounded-xl object-cover border border-zinc-100">
                                    @else
                                        <div class="w-16 h-16 rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-400">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                    @endif
                                    <div>
                                        <span class="text-xs font-semibold text-amber-500 uppercase tracking-wider">{{ $product->brand?->name ?? 'Luxury' }}</span>
                                        <h3 class="text-sm font-medium text-zinc-900 mt-0.5">{{ $product->name }}</h3>
                                        <p class="text-sm text-zinc-500 mt-1 font-semibold">${{ number_format($product->price, 2) }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-6 w-full sm:w-auto justify-between sm:justify-end">
                                    <!-- Cantidad -->
                                    <form action="{{ route('cart.update', $product) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        <label for="quantity-{{ $product->id }}" class="sr-only">Cantidad</label>
                                        <select id="quantity-{{ $product->id }}" name="quantity" onchange="this.form.submit()" class="rounded-lg border-zinc-200 text-sm py-1.5 pl-3 pr-8 focus:border-amber-500 focus:ring-amber-500 bg-white">
                                            @for($i = 1; $i <= max($product->stock, $product->quantity); $i++)
                                                <option value="{{ $i }}" {{ $product->quantity == $i ? 'selected' : '' }}>{{ $i }} ud.</option>
                                            @endfor
                                        </select>
                                    </form>

                                    <!-- Subtotal del producto -->
                                    <span class="text-sm font-semibold text-zinc-900 min-w-[80px] text-right">
                                        ${{ number_format($product->price * $product->quantity, 2) }}
                                    </span>

                                    <!-- Eliminar -->
                                    <form action="{{ route('cart.remove', $product) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-zinc-400 hover:text-red-500 transition-colors p-1" title="Eliminar artículo">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Panel de Envío y Checkout -->
            <div class="space-y-6">
                <form id="checkout-form" action="{{ route('cart.checkout') }}" method="POST" class="bg-white rounded-2xl border border-zinc-100 shadow-sm overflow-hidden p-6 space-y-6">
                    @csrf
                    
                    <h2 class="text-lg font-semibold text-zinc-900 border-b border-zinc-100 pb-4">Detalles del Pedido</h2>

                    <!-- Datos del Cliente -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold text-zinc-400 uppercase tracking-wider">Datos del Comprador</h3>
                        <div>
                            <label for="customer_name" class="block text-xs font-medium text-zinc-700">Nombre y Apellido <span class="text-red-500">*</span></label>
                            <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', auth()->user()?->name) }}" required class="mt-1 block w-full rounded-lg border-zinc-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm border px-3 py-2 outline-none">
                            @error('customer_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="customer_email" class="block text-xs font-medium text-zinc-700">Correo Electrónico <span class="text-red-500">*</span></label>
                            <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email', auth()->user()?->email) }}" required class="mt-1 block w-full rounded-lg border-zinc-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm border px-3 py-2 outline-none">
                            @error('customer_email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="customer_phone" class="block text-xs font-medium text-zinc-700">Teléfono de contacto</label>
                            <input type="text" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}" placeholder="Ej: 1144001234" class="mt-1 block w-full rounded-lg border-zinc-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm border px-3 py-2 outline-none">
                            @error('customer_phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Opciones de Envío -->
                    <div class="space-y-4 border-t border-zinc-100 pt-6">
                        <h3 class="text-sm font-semibold text-zinc-400 uppercase tracking-wider">Opciones de Envío</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Retirar en local -->
                            <label class="relative flex flex-col p-4 border rounded-xl cursor-pointer hover:bg-zinc-50 transition-all select-none border-zinc-200" id="label-pickup">
                                <input type="radio" name="shipping_option" value="pickup" class="sr-only" checked onchange="toggleShipping('pickup')">
                                <span class="text-sm font-medium text-zinc-900">Retirar en local</span>
                                <span class="text-xs text-zinc-500 mt-1">Costo: $0</span>
                            </label>
                            <!-- Enviar a domicilio -->
                            <label class="relative flex flex-col p-4 border rounded-xl cursor-pointer hover:bg-zinc-50 transition-all select-none border-zinc-200" id="label-delivery">
                                <input type="radio" name="shipping_option" value="delivery" class="sr-only" onchange="toggleShipping('delivery')">
                                <span class="text-sm font-medium text-zinc-900">A domicilio</span>
                                <span class="text-xs text-zinc-500 mt-1">Sujeto a C.P.</span>
                            </label>
                        </div>

                        <!-- Campos de Dirección (Ocultos por defecto) -->
                        <div id="shipping-address-fields" class="space-y-4 hidden">
                            <div>
                                <label for="zip_code" class="block text-xs font-medium text-zinc-700">Código Postal <span class="text-red-500">*</span></label>
                                <input type="text" name="zip_code" id="zip_code" placeholder="Ej: 4000" class="mt-1 block w-full rounded-lg border-zinc-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm border px-3 py-2 outline-none">
                                @error('zip_code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="province" class="block text-xs font-medium text-zinc-700">Provincia <span class="text-red-500">*</span></label>
                                    <input type="text" name="province" id="province" placeholder="Ej: Tucumán" class="mt-1 block w-full rounded-lg border-zinc-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm border px-3 py-2 outline-none">
                                    @error('province') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="city" class="block text-xs font-medium text-zinc-700">Ciudad <span class="text-red-500">*</span></label>
                                    <input type="text" name="city" id="city" placeholder="Ej: Yerba Buena" class="mt-1 block w-full rounded-lg border-zinc-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm border px-3 py-2 outline-none">
                                    @error('city') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div>
                                <label for="address_detail" class="block text-xs font-medium text-zinc-700">Dirección (Calle, número, piso/depto) <span class="text-red-500">*</span></label>
                                <input type="text" name="address_detail" id="address_detail" placeholder="Ej: Av. Aconquija 1200, 2° B" class="mt-1 block w-full rounded-lg border-zinc-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm border px-3 py-2 outline-none">
                                @error('address_detail') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Notas adicionales -->
                    <div class="space-y-2 border-t border-zinc-100 pt-6">
                        <label for="notes" class="block text-xs font-medium text-zinc-700">Notas / Indicaciones del pedido</label>
                        <textarea name="notes" id="notes" rows="3" placeholder="Ej: Tocar timbre de madera, dejar en recepción..." class="block w-full rounded-lg border-zinc-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm border px-3 py-2 outline-none"></textarea>
                        @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Tarjeta de Resumen de Totales -->
                    <div class="space-y-4 border-t border-zinc-100 pt-6 bg-zinc-50/50 -mx-6 -mb-6 p-6">
                        <div class="flex justify-between text-sm text-zinc-600">
                            <span>Subtotal de artículos</span>
                            <span class="font-semibold text-zinc-900">${{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-zinc-600" id="row-shipping" style="display: none;">
                            <span>Costo de envío</span>
                            <span class="font-semibold text-zinc-900" id="label-shipping-cost">$0.00</span>
                        </div>
                        <div class="flex justify-between text-base font-semibold text-zinc-900 border-t border-zinc-200 pt-4">
                            <span>Total general</span>
                            <span id="label-grand-total">${{ number_format($subtotal, 2) }}</span>
                        </div>

                        <!-- Inputs Ocultos de Costo -->
                        <input type="hidden" name="shipping_cost" id="input-shipping-cost" value="0">

                        <button type="submit" class="w-full h-12 flex items-center justify-center bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-medium tracking-wide uppercase transition-all duration-300 shadow-md hover:shadow-xl mt-6">
                            Realizar Pedido
                        </button>
                    </div>
                </form>
            </div>
            
        </div>
    @else
        <div class="text-center py-20 bg-white rounded-3xl border border-zinc-100 shadow-sm max-w-lg mx-auto">
            <svg class="w-16 h-16 text-zinc-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            <h2 class="text-xl font-medium text-zinc-900 mb-2">Tu carrito está vacío</h2>
            <p class="text-zinc-500 font-light mb-8 max-w-xs mx-auto">Explora nuestro catálogo de relojes para añadir tus piezas preferidas.</p>
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-6 h-12 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-medium uppercase tracking-wider transition-colors shadow-md">
                Ver Catálogo
            </a>
        </div>
    @endif
</div>

<!-- Lógica JavaScript para opciones de envío y cálculo de CP -->
<script>
    const subtotal = {{ $subtotal }};
    let cachedShippingCosts = {};

    function toggleShipping(option) {
        const addressFields = document.getElementById('shipping-address-fields');
        const rowShipping = document.getElementById('row-shipping');
        
        // Active visual state for labels
        const labelPickup = document.getElementById('label-pickup');
        const labelDelivery = document.getElementById('label-delivery');

        if (option === 'pickup') {
            addressFields.classList.add('hidden');
            rowShipping.style.display = 'none';

            labelPickup.classList.add('border-slate-900', 'bg-zinc-50/50');
            labelPickup.classList.remove('border-zinc-200');
            labelDelivery.classList.remove('border-slate-900', 'bg-zinc-50/50');
            labelDelivery.classList.add('border-zinc-200');

            // Set inputs
            document.getElementById('input-shipping-cost').value = 0;
            updateTotalDisplay(0);

            // Make address fields not required
            document.getElementById('zip_code').removeAttribute('required');
            document.getElementById('province').removeAttribute('required');
            document.getElementById('city').removeAttribute('required');
            document.getElementById('address_detail').removeAttribute('required');
        } else {
            addressFields.classList.remove('hidden');
            rowShipping.style.display = 'flex';

            labelDelivery.classList.add('border-slate-900', 'bg-zinc-50/50');
            labelDelivery.classList.remove('border-zinc-200');
            labelPickup.classList.remove('border-slate-900', 'bg-zinc-50/50');
            labelPickup.classList.add('border-zinc-200');

            // Make fields required
            document.getElementById('zip_code').setAttribute('required', '');
            document.getElementById('province').setAttribute('required', '');
            document.getElementById('city').setAttribute('required', '');
            document.getElementById('address_detail').setAttribute('required', '');

            // Recalculate shipping cost based on CP
            calculateShippingCost();
        }
    }

    function calculateShippingCost() {
        const zipCode = document.getElementById('zip_code').value.trim();
        const labelShipping = document.getElementById('label-shipping-cost');
        
        if (zipCode.length < 4) {
            labelShipping.innerText = 'Ingresa Código Postal';
            labelShipping.classList.add('text-zinc-400');
            document.getElementById('input-shipping-cost').value = 0;
            updateTotalDisplay(0);
            return;
        }

        labelShipping.classList.remove('text-zinc-400');

        // Check if cost already generated for this zip code
        if (!cachedShippingCosts[zipCode]) {
            // Generate random between 15000 and 35000, rounded to nearest 100
            const randomHundreds = Math.floor(Math.random() * (350 - 150 + 1) + 150);
            cachedShippingCosts[zipCode] = randomHundreds * 100;
        }

        const shippingCost = cachedShippingCosts[zipCode];
        document.getElementById('input-shipping-cost').value = shippingCost;
        labelShipping.innerText = '$' + new Intl.NumberFormat('es-AR', { minimumFractionDigits: 2 }).format(shippingCost);
        updateTotalDisplay(shippingCost);
    }

    function updateTotalDisplay(shippingCost) {
        const grandTotal = subtotal + shippingCost;
        document.getElementById('label-grand-total').innerText = '$' + new Intl.NumberFormat('es-AR', { minimumFractionDigits: 2 }).format(grandTotal);
    }

    // Set initial visual state
    document.addEventListener('DOMContentLoaded', () => {
        toggleShipping('pickup');
        
        // Listen to ZIP code changes
        document.getElementById('zip_code').addEventListener('input', calculateShippingCost);
    });
</script>
@endsection
