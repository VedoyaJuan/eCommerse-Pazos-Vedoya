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
        $finalStates = ['delivered', 'cancelled', 'anulado'];
        if (in_array($order->status, $finalStates)) {
            return back()->withErrors(['status' => 'No se puede cambiar el estado de un pedido finalizado (Entregado, Cancelado o Anulado).']);
        }

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,anulado',
        ]);

        $newStatus = $request->status;

        \Illuminate\Support\Facades\DB::transaction(function () use ($order, $newStatus) {
            if (in_array($newStatus, ['cancelled', 'anulado'])) {
                foreach ($order->items as $item) {
                    if ($item->product_id) {
                        $product = \App\Models\Product::lockForUpdate()->find($item->product_id);
                        if ($product) {
                            $product->increment('stock', $item->quantity);
                        }
                    }
                }
            }

            $order->update(['status' => $newStatus]);
        });

        return back()->with('success', 'Estado del pedido actualizado.');
    }
}
