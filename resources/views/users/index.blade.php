@extends('layouts.admin')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

    {{-- Header --}}
    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-base font-semibold text-gray-800">Usuarios Vendedores</h3>
    </div>

    {{-- Status tabs --}}
    <div class="px-6 pt-4 flex gap-2 flex-wrap">
        @php
            $tabs = [
                'pending'  => ['label' => 'Pendientes', 'color' => 'yellow'],
                'active'   => ['label' => 'Activos',    'color' => 'green'],
                'rejected' => ['label' => 'Rechazados', 'color' => 'red'],
                'all'      => ['label' => 'Todos',      'color' => 'slate'],
            ];
        @endphp

        @foreach ($tabs as $key => $tab)
            <a href="{{ route('users.index', ['status' => $key]) }}"
               class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-medium transition-colors
                      {{ $status === $key
                          ? 'bg-slate-900 text-white'
                          : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                {{ $tab['label'] }}
                @if ($key !== 'all')
                    <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[10px]
                                 {{ $status === $key ? 'bg-white/20 text-white' : 'bg-gray-300 text-gray-700' }}">
                        {{ $counts[$key] }}
                    </span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto mt-4">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 text-xs uppercase tracking-wider text-gray-400">
                    <th class="px-6 py-3 text-left">Usuario</th>
                    <th class="px-6 py-3 text-left">Email</th>
                    <th class="px-6 py-3 text-left">Estado</th>
                    <th class="px-6 py-3 text-left">Registrado</th>
                    <th class="px-6 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($users as $user)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $user->name }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        @if ($user->status === 'active')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Activo</span>
                        @elseif ($user->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Pendiente</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Rechazado</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-400 text-xs">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="inline-flex items-center gap-3">
                            @if ($user->status !== 'active')
                                <form method="POST" action="{{ route('users.approve', $user) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-green-600 hover:text-green-800 font-medium transition-colors">
                                        Aprobar
                                    </button>
                                </form>
                            @endif

                            @if ($user->status !== 'rejected')
                                <form method="POST" action="{{ route('users.reject', $user) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-yellow-600 hover:text-yellow-800 font-medium transition-colors">
                                        Rechazar
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('users.destroy', $user) }}"
                                  onsubmit="return confirm('¿Eliminás a {{ addslashes($user->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 font-medium transition-colors">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                        No hay usuarios en este estado.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $users->appends(['status' => $status])->links() }}
        </div>
    @endif

</div>
@endsection
