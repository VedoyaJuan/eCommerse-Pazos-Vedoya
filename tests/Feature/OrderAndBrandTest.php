<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderAndBrandTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a product via Web controller.
     */
    public function test_creating_product_via_web_creates_brand_and_associates_it()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->post(route('products.store'), [
            'name' => 'Test Watch',
            'description' => 'A description',
            'price' => 150.00,
            'stock' => 10,
            'brand' => 'Rolex',
            'image_url' => 'https://example.com/watch.jpg',
        ]);

        $response->assertRedirect(route('products.index'));

        // Assert Brand was created
        $brand = Brand::where('name', 'Rolex')->first();
        $this->assertNotNull($brand);

        // Assert Product is linked to Brand
        $product = Product::where('name', 'Test Watch')->first();
        $this->assertNotNull($product);
        $this->assertEquals($brand->id, $product->brand_id);
        $this->assertEquals('Rolex', $product->brand->name);
    }

    /**
     * Test updating a product via Web controller.
     */
    public function test_updating_product_via_web_changes_or_creates_brand()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $brand = Brand::create(['name' => 'Omega']);
        $product = Product::create([
            'name' => 'Test Watch',
            'description' => 'A description',
            'price' => 150.00,
            'stock' => 10,
            'brand_id' => $brand->id,
            'image_url' => 'https://example.com/watch.jpg',
        ]);

        $response = $this->actingAs($user)->put(route('products.update', $product), [
            'name' => 'Test Watch',
            'description' => 'A description',
            'price' => 150.00,
            'stock' => 10,
            'brand' => 'Tag Heuer',
            'image_url' => 'https://example.com/watch.jpg',
        ]);

        $response->assertRedirect(route('products.index'));

        // Assert new Brand was created
        $newBrand = Brand::where('name', 'Tag Heuer')->first();
        $this->assertNotNull($newBrand);

        $product->refresh();
        $this->assertEquals($newBrand->id, $product->brand_id);
    }

    /**
     * Test filtering products by brand name via API.
     */
    public function test_filtering_products_by_brand_via_api()
    {
        $brand1 = Brand::create(['name' => 'Casio']);
        $brand2 = Brand::create(['name' => 'Seiko']);

        Product::create([
            'name' => 'Casio F-91W',
            'price' => 20.00,
            'stock' => 5,
            'brand_id' => $brand1->id,
        ]);

        Product::create([
            'name' => 'Seiko 5',
            'price' => 150.00,
            'stock' => 3,
            'brand_id' => $brand2->id,
        ]);

        // Call API index without auth (since products index is public route)
        $response = $this->getJson('/api/products?brand=Casio');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.name', 'Casio F-91W')
                 ->assertJsonPath('data.0.brand', 'Casio');
    }

    /**
     * Test web order status update restriction if order is delivered.
     */
    public function test_web_order_status_cannot_be_updated_if_already_delivered()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'delivered',
            'total' => 100.00,
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'shipping_address' => '123 St',
        ]);

        $response = $this->actingAs($user)->put(route('orders.update', $order), [
            'status' => 'processing',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('status');

        $order->refresh();
        $this->assertEquals('delivered', $order->status);
    }

    /**
     * Test API order status update restriction if order is delivered.
     */
    public function test_api_order_status_cannot_be_updated_if_already_delivered()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'delivered',
            'total' => 100.00,
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'shipping_address' => '123 St',
        ]);

        $response = $this->actingAs($user)->patchJson("/api/admin/orders/{$order->id}", [
            'status' => 'processing',
        ]);

        $response->assertStatus(422)
                 ->assertJsonPath('message', 'No se puede cambiar el estado de un pedido finalizado (Entregado, Cancelado o Anulado).');

        $order->refresh();
        $this->assertEquals('delivered', $order->status);
    }



    /**
     * Test stock return and status locking for cancelled order (web).
     */
    public function test_web_order_cancelled_returns_stock_and_locks_status()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $product = Product::create([
            'name' => 'Casio F-91W',
            'price' => 50000.00,
            'stock' => 8,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'total' => 100000.00,
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'shipping_address' => '123 St',
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 50000.00,
        ]);

        // Change status to cancelled
        $response = $this->actingAs($user)->put(route('orders.update', $order), [
            'status' => 'cancelled',
        ]);

        $response->assertRedirect();
        
        // Assert status updated to cancelled
        $order->refresh();
        $this->assertEquals('cancelled', $order->status);

        // Assert stock returned (8 + 2 = 10)
        $product->refresh();
        $this->assertEquals(10, $product->stock);

        // Try to update from cancelled to processing
        $response2 = $this->actingAs($user)->put(route('orders.update', $order), [
            'status' => 'processing',
        ]);

        $response2->assertRedirect();
        $response2->assertSessionHasErrors('status');

        $order->refresh();
        $this->assertEquals('cancelled', $order->status);
    }

    /**
     * Test stock return and status locking for anulado order (API).
     */
    public function test_api_order_anulado_returns_stock_and_locks_status()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $product = Product::create([
            'name' => 'Casio F-91W',
            'price' => 50000.00,
            'stock' => 5,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'total' => 150000.00,
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'shipping_address' => '123 St',
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 3,
            'unit_price' => 50000.00,
        ]);

        // Change status to anulado via API
        $response = $this->actingAs($user)->patchJson("/api/admin/orders/{$order->id}", [
            'status' => 'anulado',
        ]);

        $response->assertStatus(200);

        // Assert status updated to anulado
        $order->refresh();
        $this->assertEquals('anulado', $order->status);

        // Assert stock returned (5 + 3 = 8)
        $product->refresh();
        $this->assertEquals(8, $product->stock);

        // Try to update from anulado to shipped
        $response2 = $this->actingAs($user)->patchJson("/api/admin/orders/{$order->id}", [
            'status' => 'shipped',
        ]);

        $response2->assertStatus(422)
                  ->assertJsonPath('message', 'No se puede cambiar el estado de un pedido finalizado (Entregado, Cancelado o Anulado).');

        $order->refresh();
        $this->assertEquals('anulado', $order->status);
    }
}
