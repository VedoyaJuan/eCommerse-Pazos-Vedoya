@extends('layouts.admin')

@section('title', 'Gestión de Productos')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900">Listado de Relojes</h3>
        <a href="{{ route('products.create') }}" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm">
            + Nuevo Producto
        </a>
    </div>

    <!-- Buscador y Filtros -->
    <div class="p-5 border-b border-gray-100 bg-gray-50/50">
        <form action="{{ route('products.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <label for="search" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Buscar producto</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nombre, marca o descripción..." 
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm border px-3 py-2 outline-none transition-colors">
            </div>
            <div class="w-full md:w-48">
                <label for="max_price" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Precio máximo ($)</label>
                <input type="number" name="max_price" id="max_price" value="{{ request('max_price') }}" placeholder="Ej: 150000" step="0.01"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm border px-3 py-2 outline-none transition-colors">
            </div>
            <div class="w-full md:w-32">
                <label for="min_stock" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Stock mín.</label>
                <input type="number" name="min_stock" id="min_stock" value="{{ request('min_stock') }}" placeholder="Ej: 5"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm border px-3 py-2 outline-none transition-colors">
            </div>
            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="flex-1 md:flex-none bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm">
                    Filtrar
                </button>
                <a href="{{ route('products.index') }}" class="flex-1 md:flex-none bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm text-center">
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
                    <th class="px-6 py-4 font-medium">Imagen</th>
                    <th class="px-6 py-4 font-medium">Nombre</th>
                    <th class="px-6 py-4 font-medium">Marca</th>
                    <th class="px-6 py-4 font-medium">Precio</th>
                    <th class="px-6 py-4 font-medium">Stock</th>
                    <th class="px-6 py-4 font-medium text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($products as $product)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 text-sm text-gray-500">#{{ $product->id }}</td>
                    <td class="px-6 py-4">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-10 h-10 rounded-full object-cover border border-gray-200">
                        @else
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-400 text-xs">N/A</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $product->name }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $product->brand ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">${{ number_format($product->price, 2) }}</td>
                    <td class="px-6 py-4 text-sm">
                        @if($product->stock > 0)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $product->stock }} un.
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Sin stock
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                        <a href="{{ route('products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900 transition-colors">Editar</a>
                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de eliminar este producto?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 transition-colors">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        No hay productos registrados aún.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($products->hasPages())
    <div class="p-4 border-t border-gray-100">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection
