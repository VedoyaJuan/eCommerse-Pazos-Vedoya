@extends('layouts.admin')

@section('title', 'Pedido #' . $order->id)

@section('content')
@php
    $statusMap = [
        'pending'    => ['label' => 'Pendiente',   'class' => 'bg-yellow-100 text-yellow-800'],
        'processing' => ['label' => 'En proceso',  'class' => 'bg-blue-100 text-blue-800'],
        'shipped'    => ['label' => 'Enviado',     'class' => 'bg-indigo-100 text-indigo-800'],
        'delivered'  => ['label' => 'Entregado',   'class' => 'bg-green-100 text-green-800'],
        'cancelled'  => ['label' => 'Cancelado',   'class' => 'bg-red-100 text-red-800'],
        'anulado'    => ['label' => 'Anulado',     'class' => 'bg-gray-200 text-gray-700'],
    ];
    $badge = $statusMap[$order->status] ?? ['label' => $order->status, 'class' => 'bg-gray-100 text-gray-800'];
@endphp

<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('orders.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition-colors flex items-center gap-1">
            ← Volver a pedidos
        </a>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $badge['class'] }}">
            {{ $badge['label'] }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Datos del cliente -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-4">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Cliente</h3>
            <div>
                <p class="font-medium text-gray-900">{{ $order->customer_name }}</p>
                <p class="text-sm text-gray-500">{{ $order->customer_email }}</p>
                @if($order->customer_phone)
                <p class="text-sm text-gray-500">{{ $order->customer_phone }}</p>
                @endif
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Dirección de envío</p>
                <p class="text-sm text-gray-700">{{ $order->shipping_address }}</p>
            </div>
            @if($order->notes)
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Notas</p>
                <p class="text-sm text-gray-700">{{ $order->notes }}</p>
            </div>
            @endif
            <div class="pt-2 border-t border-gray-100">
                <p class="text-xs text-gray-400">Pedido el {{ $order->created_at->format('d/m/Y \a \l\a\s H:i') }}</p>
            </div>
        </div>

        <!-- Cambiar estado -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-4">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Cambiar estado</h3>
            @php
                $isFinal = in_array($order->status, ['delivered', 'cancelled', 'anulado']);
            @endphp
            @if($order->status === 'delivered')
                <p class="text-sm text-green-600 font-medium bg-green-50 p-2.5 rounded-md border border-green-100">El pedido ha sido entregado y su estado ya no puede ser modificado.</p>
            @elseif($order->status === 'cancelled')
                <p class="text-sm text-red-600 font-medium bg-red-50 p-2.5 rounded-md border border-red-100">El pedido ha sido cancelado y su estado ya no puede ser modificado.</p>
            @elseif($order->status === 'anulado')
                <p class="text-sm text-gray-600 font-medium bg-gray-100 p-2.5 rounded-md border border-gray-200">El pedido ha sido anulado y su estado ya no puede ser modificado.</p>
            @endif
            <form action="{{ route('orders.update', $order) }}" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                <select name="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm border px-3 py-2 outline-none transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed" {{ $isFinal ? 'disabled' : '' }}>
                    <option value="pending"    {{ $order->status === 'pending'    ? 'selected' : '' }}>Pendiente</option>
                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>En proceso</option>
                    <option value="shipped"    {{ $order->status === 'shipped'    ? 'selected' : '' }}>Enviado</option>
                    <option value="delivered"  {{ $order->status === 'delivered'  ? 'selected' : '' }}>Entregado</option>
                    <option value="cancelled"  {{ $order->status === 'cancelled'  ? 'selected' : '' }}>Cancelado</option>
                    <option value="anulado"    {{ $order->status === 'anulado'    ? 'selected' : '' }}>Anulado</option>
                </select>
                <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm disabled:bg-gray-300 disabled:cursor-not-allowed" {{ $isFinal ? 'disabled' : '' }}>
                    Actualizar estado
                </button>
            </form>
        </div>

        <!-- Resumen de totales -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-4">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Resumen</h3>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Artículos</span>
                    <span class="text-gray-900">{{ $order->items->count() }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Unidades</span>
                    <span class="text-gray-900">{{ $order->items->sum('quantity') }}</span>
                </div>
                <div class="flex justify-between text-sm border-t border-gray-100 pt-2">
                    <span class="text-gray-500">Envío ({{ $order->shipping_option === 'delivery' ? 'Domicilio' : 'Retiro en local' }})</span>
                    <span class="text-gray-900">${{ number_format($order->shipping_cost, 2) }}</span>
                </div>
                <div class="flex justify-between font-semibold text-base pt-2 border-t border-gray-100">
                    <span class="text-gray-900">Total</span>
                    <span class="text-gray-900">${{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Items del pedido -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-medium text-gray-900">Productos del pedido</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-medium">Producto</th>
                        <th class="px-6 py-4 font-medium">Marca</th>
                        <th class="px-6 py-4 font-medium text-right">Precio unit.</th>
                        <th class="px-6 py-4 font-medium text-right">Cantidad</th>
                        <th class="px-6 py-4 font-medium text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($item->product?->image_url)
                                    <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-400 text-xs">N/A</div>
                                @endif
                                <span class="font-medium text-gray-900">{{ $item->product?->name ?? 'Producto eliminado' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->product?->brand?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-900">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-900">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 text-sm text-right font-medium text-gray-900">${{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
