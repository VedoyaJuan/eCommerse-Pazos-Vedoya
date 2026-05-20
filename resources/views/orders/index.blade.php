@extends('layouts.admin')

@section('title', 'Gestión de Pedidos')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100">
        <h3 class="text-lg font-medium text-gray-900">Listado de Pedidos</h3>
    </div>

    <!-- Filtros -->
    <div class="p-5 border-b border-gray-100 bg-gray-50/50">
        <form action="{{ route('orders.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <label for="search" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Buscar cliente</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nombre o email..."
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm border px-3 py-2 outline-none transition-colors">
            </div>
            <div class="w-full md:w-48">
                <label for="status" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Estado</label>
                <select name="status" id="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm border px-3 py-2 outline-none transition-colors">
                    <option value="">Todos</option>
                    <option value="pending"     {{ request('status') === 'pending'     ? 'selected' : '' }}>Pendiente</option>
                    <option value="processing"  {{ request('status') === 'processing'  ? 'selected' : '' }}>En proceso</option>
                    <option value="shipped"     {{ request('status') === 'shipped'     ? 'selected' : '' }}>Enviado</option>
                    <option value="delivered"   {{ request('status') === 'delivered'   ? 'selected' : '' }}>Entregado</option>
                    <option value="cancelled"   {{ request('status') === 'cancelled'   ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="flex-1 md:flex-none bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm">
                    Filtrar
                </button>
                <a href="{{ route('orders.index') }}" class="flex-1 md:flex-none bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm text-center">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-medium">ID</th>
                    <th class="px-6 py-4 font-medium">Cliente</th>
                    <th class="px-6 py-4 font-medium">Total</th>
                    <th class="px-6 py-4 font-medium">Estado</th>
                    <th class="px-6 py-4 font-medium">Fecha</th>
                    <th class="px-6 py-4 font-medium text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                @php
                    $statusMap = [
                        'pending'    => ['label' => 'Pendiente',   'class' => 'bg-yellow-100 text-yellow-800'],
                        'processing' => ['label' => 'En proceso',  'class' => 'bg-blue-100 text-blue-800'],
                        'shipped'    => ['label' => 'Enviado',     'class' => 'bg-indigo-100 text-indigo-800'],
                        'delivered'  => ['label' => 'Entregado',   'class' => 'bg-green-100 text-green-800'],
                        'cancelled'  => ['label' => 'Cancelado',   'class' => 'bg-red-100 text-red-800'],
                    ];
                    $badge = $statusMap[$order->status] ?? ['label' => $order->status, 'class' => 'bg-gray-100 text-gray-800'];
                @endphp
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 text-sm text-gray-500">#{{ $order->id }}</td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $order->customer_name }}</div>
                        <div class="text-xs text-gray-500">{{ $order->customer_email }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">${{ number_format($order->total, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge['class'] }}">
                            {{ $badge['label'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                        <a href="{{ route('orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900 transition-colors">Ver</a>
                        <form action="{{ route('orders.destroy', $order) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Eliminar este pedido?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 transition-colors">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">No hay pedidos registrados aún.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
    <div class="p-4 border-t border-gray-100">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection
