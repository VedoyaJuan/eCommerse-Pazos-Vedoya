<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiCartTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting cart items via API.
     */
    public function test_can_get_cart_items_via_api()
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Casio Vintage',
            'price' => 25000.00,
            'stock' => 10,
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/cart');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.quantity', 2)
                 ->assertJsonPath('data.0.product.name', 'Casio Vintage')
                 ->assertJsonPath('data.0.subtotal', 50000);
    }

    /**
     * Test adding a product to the cart via API.
     */
    public function test_can_add_product_to_cart_via_api()
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Casio Vintage',
            'price' => 25000.00,
            'stock' => 10,
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/cart', [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.quantity', 3)
                 ->assertJsonPath('data.product.name', 'Casio Vintage');

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);
    }

    /**
     * Test adding an existing product to the cart increments its quantity.
     */
    public function test_adding_existing_product_increments_quantity()
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Casio Vintage',
            'price' => 25000.00,
            'stock' => 10,
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/cart', [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.quantity', 5);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 5,
        ]);
    }

    /**
     * Test that adding items respects the product stock limit.
     */
    public function test_adding_item_enforces_stock_limit()
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Casio Vintage',
            'price' => 25000.00,
            'stock' => 4,
        ]);

        // Attempting to add 5 items (exceeds stock of 4)
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/cart', [
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        // Should cap it at the stock value
        $response->assertStatus(201)
                 ->assertJsonPath('data.quantity', 4);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 4,
        ]);
    }

    /**
     * Test updating cart item quantity.
     */
    public function test_can_update_cart_quantity_via_api()
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Casio Vintage',
            'price' => 25000.00,
            'stock' => 10,
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/cart/{$product->id}", [
            'quantity' => 4,
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.quantity', 4);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 4,
        ]);
    }

    /**
     * Test removing product from cart.
     */
    public function test_can_remove_product_from_cart_via_api()
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Casio Vintage',
            'price' => 25000.00,
            'stock' => 10,
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/cart/{$product->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Reloj removido del carrito.');

        $this->assertDatabaseMissing('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    /**
     * Test checkout via API.
     */
    public function test_can_checkout_cart_via_api()
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Casio Vintage',
            'price' => 25000.00,
            'stock' => 10,
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/cart/checkout', [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '1144001234',
            'shipping_option' => 'delivery',
            'shipping_cost' => 15000.00,
            'shipping_address' => 'Av. Siempreviva 742, Tucumán',
            'notes' => 'Tocar timbre',
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.customer_name', 'John Doe')
                 ->assertJsonPath('data.total', 65000) // 25000 * 2 + 15000
                 ->assertJsonPath('data.shipping_option', 'delivery');

        // Check order created in DB
        $order = Order::latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals($user->id, $order->user_id);
        $this->assertEquals(65000.00, $order->total);

        // Check stock decremented
        $product->refresh();
        $this->assertEquals(8, $product->stock);

        // Check cart emptied
        $this->assertDatabaseMissing('cart_items', [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test checkout fails if cart is empty.
     */
    public function test_checkout_fails_if_cart_is_empty()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/cart/checkout', [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'shipping_address' => 'Av. Siempreviva 742',
        ]);

        $response->assertStatus(422)
                 ->assertJsonPath('message', 'El carrito está vacío.');
    }

    /**
     * Test checkout fails if stock is insufficient.
     */
    public function test_checkout_fails_if_stock_is_insufficient()
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Casio Vintage',
            'price' => 25000.00,
            'stock' => 1,
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2, // Stock is only 1!
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/cart/checkout', [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'shipping_address' => 'Av. Siempreviva 742',
        ]);

        $response->assertStatus(422)
                 ->assertJsonPath('message', 'Stock insuficiente para "Casio Vintage".');

        // Check stock was not decremented
        $product->refresh();
        $this->assertEquals(1, $product->stock);

        // Check cart was not emptied
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }
}
