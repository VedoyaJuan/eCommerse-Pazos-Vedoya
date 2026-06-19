<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Display the shopping cart.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $products = [];
        $subtotal = 0;

        if (!empty($cart)) {
            // Fetch products in cart
            $products = Product::with('brand')->whereIn('id', array_keys($cart))->get();
            
            // Map quantity and calculate subtotal
            foreach ($products as $product) {
                $product->quantity = $cart[$product->id];
                $subtotal += $product->price * $product->quantity;
            }
        }

        return view('cart.index', compact('products', 'subtotal'));
    }

    /**
     * Add a product to the cart.
     */
    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . max(1, $product->stock),
        ]);

        $cart = session()->get('cart', []);
        $id = $product->id;

        if (isset($cart[$id])) {
            $cart[$id] += $request->quantity;
        } else {
            $cart[$id] = $request->quantity;
        }

        // Enforce stock limit
        if ($cart[$id] > $product->stock) {
            $cart[$id] = $product->stock;
        }

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Reloj añadido al carrito.');
    }

    /**
     * Update the quantity of a product in the cart.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . max(1, $product->stock),
        ]);

        $cart = session()->get('cart', []);
        $id = $product->id;

        if (isset($cart[$id])) {
            $cart[$id] = $request->quantity;
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Cantidad actualizada.');
    }

    /**
     * Remove a product from the cart.
     */
    public function remove(Product $product)
    {
        $cart = session()->get('cart', []);
        $id = $product->id;

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Reloj removido del carrito.');
    }

    /**
     * Handle the checkout and order placement.
     */
    public function checkout(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('home')->with('error', 'El carrito está vacío.');
        }

        $shippingOption = $request->input('shipping_option');
        $rules = [
            'customer_name'   => 'required|string|max:255',
            'customer_email'  => 'required|email|max:255',
            'customer_phone'  => 'nullable|string|max:50',
            'shipping_option' => 'required|in:pickup,delivery',
            'notes'           => 'nullable|string|max:1000',
        ];

        if ($shippingOption === 'delivery') {
            $rules['zip_code']       = 'required|string|max:20';
            $rules['province']       = 'required|string|max:255';
            $rules['city']           = 'required|string|max:255';
            $rules['address_detail'] = 'required|string|max:255';
            $rules['shipping_cost']  = 'required|numeric|min:15000|max:35000';
        }

        $validated = $request->validate($rules);

        $shippingCost = 0;
        $shippingAddress = "Retiro en local (Sucursal)";

        if ($shippingOption === 'delivery') {
            $shippingCost = (float) $validated['shipping_cost'];
            // Round to hundreds to ensure compliance
            if ($shippingCost % 100 !== 0) {
                $shippingCost = round($shippingCost / 100) * 100;
            }
            $shippingAddress = "{$validated['address_detail']}, C.P. {$validated['zip_code']}, {$validated['city']}, {$validated['province']}";
        }

        // Place the order in a DB transaction
        $order = DB::transaction(function () use ($cart, $validated, $shippingOption, $shippingCost, $shippingAddress) {
            $total = $shippingCost;
            $lines = [];

            foreach ($cart as $productId => $quantity) {
                $product = Product::lockForUpdate()->findOrFail($productId);

                if ($product->stock < $quantity) {
                    abort(422, "Stock insuficiente para \"{$product->name}\".");
                }

                $subtotal = $product->price * $quantity;
                $total += $subtotal;

                $product->decrement('stock', $quantity);

                $lines[] = [
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $product->price,
                ];
            }

            $order = Order::create([
                'user_id'          => auth()->id(),
                'status'           => 'pending',
                'total'            => $total,
                'customer_name'    => $validated['customer_name'],
                'customer_email'   => $validated['customer_email'],
                'customer_phone'   => $validated['customer_phone'] ?? null,
                'shipping_address' => $shippingAddress,
                'shipping_option'  => $shippingOption,
                'shipping_cost'    => $shippingCost,
                'notes'            => $validated['notes'] ?? null,
            ]);

            $order->items()->createMany($lines);

            return $order;
        });

        // Clear the cart
        session()->forget('cart');

        return redirect()->route('orders.show', $order)->with('success', 'Pedido creado exitosamente.');
    }
}
