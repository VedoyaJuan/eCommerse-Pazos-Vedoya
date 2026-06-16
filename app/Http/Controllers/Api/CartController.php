<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartItemResource;
use App\Http\Resources\OrderResource;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Display the authenticated user's cart.
     */
    public function index(Request $request)
    {
        $cartItems = $request->user()
            ->cartItems()
            ->with('product.brand')
            ->get();

        return CartItemResource::collection($cartItems);
    }

    /**
     * Add a product to the cart.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock <= 0) {
            return response()->json([
                'message' => "El producto \"{$product->name}\" no tiene stock disponible."
            ], 422);
        }

        // Get existing cart item
        $cartItem = $request->user()
            ->cartItems()
            ->where('product_id', $product->id)
            ->first();

        $newQuantity = ($cartItem ? $cartItem->quantity : 0) + $request->quantity;

        // Enforce stock limit
        if ($newQuantity > $product->stock) {
            $newQuantity = $product->stock;
        }

        $cartItem = CartItem::updateOrCreate(
            [
                'user_id'    => $request->user()->id,
                'product_id' => $product->id,
            ],
            [
                'quantity'   => $newQuantity,
            ]
        );

        $cartItem->load('product.brand');

        return new CartItemResource($cartItem);
    }

    /**
     * Update the quantity of a product in the cart.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . max(1, $product->stock),
        ]);

        $cartItem = $request->user()
            ->cartItems()
            ->where('product_id', $product->id)
            ->firstOrFail();

        $cartItem->update([
            'quantity' => $request->quantity,
        ]);

        $cartItem->load('product.brand');

        return new CartItemResource($cartItem);
    }

    /**
     * Remove a product from the cart.
     */
    public function destroy(Request $request, Product $product)
    {
        $cartItem = $request->user()
            ->cartItems()
            ->where('product_id', $product->id)
            ->firstOrFail();

        $cartItem->delete();

        return response()->json([
            'message' => 'Reloj removido del carrito.'
        ]);
    }

    /**
     * Checkout the cart and create an order.
     */
    public function checkout(Request $request)
    {
        $cartItems = $request->user()
            ->cartItems()
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'message' => 'El carrito está vacío.'
            ], 422);
        }

        $validated = $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_email'   => 'required|email|max:255',
            'customer_phone'   => 'nullable|string|max:50',
            'shipping_address' => 'required|string|max:500',
            'shipping_option'  => 'nullable|in:pickup,delivery',
            'shipping_cost'    => 'nullable|numeric|min:0',
            'notes'            => 'nullable|string|max:1000',
        ]);

        $order = DB::transaction(function () use ($request, $cartItems, $validated) {
            $shippingCost = (float) ($validated['shipping_cost'] ?? 0.00);
            $total = $shippingCost;
            $lines = [];

            foreach ($cartItems as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);

                if (!$product) {
                    abort(422, "El producto ID {$item->product_id} ya no está disponible.");
                }

                if ($product->stock < $item->quantity) {
                    abort(422, "Stock insuficiente para \"{$product->name}\".");
                }

                $subtotal = $product->price * $item->quantity;
                $total   += $subtotal;

                $product->decrement('stock', $item->quantity);

                $lines[] = [
                    'product_id' => $product->id,
                    'quantity'   => $item->quantity,
                    'unit_price' => $product->price,
                ];
            }

            // Create order
            $order = Order::create([
                'user_id'          => $request->user()->id,
                'status'           => 'pending',
                'total'            => $total,
                'customer_name'    => $validated['customer_name'],
                'customer_email'   => $validated['customer_email'],
                'customer_phone'   => $validated['customer_phone'] ?? null,
                'shipping_address' => $validated['shipping_address'],
                'shipping_option'  => $validated['shipping_option'] ?? 'pickup',
                'shipping_cost'    => $shippingCost,
                'notes'            => $validated['notes'] ?? null,
            ]);

            // Save order items
            $order->items()->createMany($lines);

            // Empty the cart
            $request->user()->cartItems()->delete();

            return $order;
        });

        $order->load('items.product.brand');

        return new OrderResource($order);
    }
}
