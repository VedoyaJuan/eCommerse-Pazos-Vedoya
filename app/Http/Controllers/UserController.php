<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $users = User::where('role', 'vendedor')
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15);

        $counts = [
            'pending'  => User::where('role', 'vendedor')->where('status', 'pending')->count(),
            'active'   => User::where('role', 'vendedor')->where('status', 'active')->count(),
            'rejected' => User::where('role', 'vendedor')->where('status', 'rejected')->count(),
        ];

        return view('users.index', compact('users', 'status', 'counts'));
    }

    public function approve(User $user)
    {
        $user->update(['status' => 'active']);

        return back()->with('success', "Usuario {$user->name} aprobado correctamente.");
    }

    public function reject(User $user)
    {
        $user->update(['status' => 'rejected']);

        return back()->with('success', "Usuario {$user->name} rechazado.");
    }

    public function destroy(User $user)
    {
        $user->delete();

        return back()->with('success', 'Usuario eliminado.');
    }
}
