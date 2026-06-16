<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('items.product.brand');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'ilike', "%{$search}%")
                  ->orWhere('customer_email', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items.product.brand');
        return view('orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        if ($order->status === 'delivered') {
            return back()->withErrors(['status' => 'No se puede cambiar el estado de un pedido ya entregado.']);
        }

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,anulado',
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Estado del pedido actualizado.');
    }
}
