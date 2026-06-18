<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->with('items.product')
            ->latest()
            ->paginate(15);

        return OrderResource::collection($orders);
    }

    public function show(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $order->load('items.product');

        return new OrderResource($order);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_email'   => 'required|email|max:255',
            'customer_phone'   => 'nullable|string|max:50',
            'shipping_address' => 'required|string|max:500',
            'notes'            => 'nullable|string|max:1000',
            'items'            => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        $order = DB::transaction(function () use ($data, $request) {
            $total = 0;
            $lines = [];

            foreach ($data['items'] as $line) {
                $product = Product::lockForUpdate()->findOrFail($line['product_id']);

                if ($product->stock < $line['quantity']) {
                    abort(422, "Stock insuficiente para \"{$product->name}\".");
                }

                $subtotal = $product->price * $line['quantity'];
                $total   += $subtotal;

                $product->decrement('stock', $line['quantity']);

                $lines[] = [
                    'product_id' => $product->id,
                    'quantity'   => $line['quantity'],
                    'unit_price' => $product->price,
                ];
            }

            $order = Order::create([
                'user_id'          => $request->user()?->id,
                'status'           => 'pending',
                'total'            => $total,
                'customer_name'    => $data['customer_name'],
                'customer_email'   => $data['customer_email'],
                'customer_phone'   => $data['customer_phone'] ?? null,
                'shipping_address' => $data['shipping_address'],
                'notes'            => $data['notes'] ?? null,
                'access_token'     => Str::random(32),
            ]);

            $order->items()->createMany($lines);

            return $order;
        });

        $order->load('items.product');

        return response()->json([
            'id' => $order->id,
            'access_token' => $order->access_token,
            'customer_name' => $order->customer_name,
            'customer_email' => $order->customer_email,
            'customer_phone' => $order->customer_phone,
            'shipping_address' => $order->shipping_address,
            'shipping_cost' => 0,
            'notes' => $order->notes,
            'status' => $order->status,
            'total' => $order->total,
            'created_at' => $order->created_at,
            'items' => $order->items->map(fn($item) => [
                'id' => $item->id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'subtotal' => $item->quantity * $item->unit_price,
                'product' => [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'image' => $item->product->image,
                ]
            ]),
        ], 201);
    }

    public function adminIndex(Request $request)
    {
        $query = Order::with('items.product');

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

        $orders = $query->latest()->paginate(15);

        return OrderResource::collection($orders);
    }

    public function adminShow(Order $order)
    {
        $order->load('items.product');
        return new OrderResource($order);
    }

    public function adminUpdate(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,anulado',
        ]);

        $order->update(['status' => $request->status]);

        $order->load('items.product');
        return new OrderResource($order);
    }

    public function showGuest($token, Request $request)
    {
        $email = $request->query('email');

        if (!$email) {
            return response()->json(['error' => 'Email required'], 400);
        }

        $order = Order::where('access_token', $token)
            ->where('customer_email', $email)
            ->with('items.product')
            ->firstOrFail();

        return response()->json([
            'id' => $order->id,
            'access_token' => $order->access_token,
            'customer_name' => $order->customer_name,
            'customer_email' => $order->customer_email,
            'customer_phone' => $order->customer_phone,
            'shipping_address' => $order->shipping_address,
            'shipping_cost' => 0,
            'notes' => $order->notes,
            'status' => $order->status,
            'total' => $order->total,
            'created_at' => $order->created_at,
            'items' => $order->items->map(fn($item) => [
                'id' => $item->id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'subtotal' => $item->quantity * $item->unit_price,
                'product' => [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'image' => $item->product->image,
                ]
            ]),
        ]);
    }

    public function cancelGuest($token, Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $order = Order::where('access_token', $token)
            ->where('customer_email', $validated['email'])
            ->firstOrFail();

        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'error' => 'No se puede cancelar un pedido en estado ' . $order->status,
                'current_status' => $order->status,
            ], 422);
        }

        $order->update(['status' => 'cancelled']);

        return response()->json([
            'id' => $order->id,
            'status' => $order->status,
            'message' => 'Pedido cancelado exitosamente',
            'cancelled_at' => now(),
        ]);
    }
}
