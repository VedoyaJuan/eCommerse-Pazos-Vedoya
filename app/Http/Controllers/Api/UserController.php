<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

        return response()->json([
            'users'  => $users,
            'status' => $status,
            'counts' => $counts,
        ]);
    }

    public function approve(User $user)
    {
        $user->update(['status' => 'active']);

        return response()->json([
            'message' => "Usuario {$user->name} aprobado correctamente.",
            'user'    => $user,
        ]);
    }

    public function reject(User $user)
    {
        $user->update(['status' => 'rejected']);

        return response()->json([
            'message' => "Usuario {$user->name} rechazado.",
            'user'    => $user,
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'Usuario eliminado.',
        ]);
    }
}
