<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\WithFaker;

class ProductsTest extends TestCase
{
    // Caution: Do not run your test on real database

    private User $user;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    public function test_homepage_contains_empty_table(): void
    {
        $response = $this->actingAs($this->user)->get('/products');

        $response->assertStatus(200);
        $response->assertSee(__('No products found'));
    }

    public function test_homepage_contains_non_empty_table(): void
    {
        $product = Product::create([
            'name' => 'test',
            'price' => 1999
        ]);

        $response = $this->actingAs($this->user)->get('/products');

        $response->assertStatus(200);
        $response->assertDontSee(__('No products found'));
        $response->assertViewHas('products', function ($collection) use ($product) {
            return $collection->contains($product);
        });
    }

    public function test_paginated_products_table_doesnt_contain_11th_record()
    {
        $products = Product::factory(11)->create();

        $lastProduct = $products->last();

        $response = $this->actingAs($this->user)->get('/products');

        $response->assertStatus(200);

        // Assert 11th Product doesn't exist in first page
        $response->assertViewHas('products', function ($collection) use ($lastProduct) {
            return !$collection->contains($lastProduct);
        });
    }

    public function test_admin_can_see_product_create_button()
    {
        $response = $this->actingAs($this->admin)->get('/products');

        $response->assertStatus(200);
        $response->assertSee('ADD NEW PRODUCT');
    }

    public function test_non_admin_cannot_see_product_create_button()
    {
        $response = $this->actingAs($this->user)->get('/products');

        $response->assertStatus(200);
        $response->assertDontSee('ADD NEW PRODUCT');
    }

    public function test_admin_can_access_product_create_page()
    {
        $response = $this->actingAs($this->admin)->get('/products/create');

        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_access_product_create_page()
    {
        $response = $this->actingAs($this->user)->get('/products/create');

        $response->assertStatus(403);
    }

    public function test_create_product_successful()
    {
        $product = [
            'name' => 'Dell Laptop',
            'price' => 1000
        ];

        $response = $this->actingAs($this->admin)->post('/products/create', $product);

        $response->assertStatus(302);
        $response->assertRedirect('products');

        // Product exists in db
        $this->assertDatabaseHas('products', $product);

        // If Product is the latest one
        $lastProduct = Product::latest()->first();
        $this->assertEquals($lastProduct->name, $product['name']);
        $this->assertEquals($lastProduct->price, $product['price']);
    }

    public function test_product_edit_contains_correct_values()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)->get("products/edit/$product->id");

        $response->assertStatus(200);
        $response->assertSee("value='" . $product->name . "'", false);
        $response->assertSee("value='" . $product->price . "'", false);
        $response->assertViewHas('product', $product);
    }

    public function test_product_update_validation_error_redirects_back_to_form()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)->put("/products/update/$product->id", [
            'name' => '',
            'price' => ''
        ]);

        $response->assertStatus(302);
        $response->assertInvalid(["name", "price"]);
    }

    public function test_product_delete_successful()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)->delete("/products/delete/$product->id");

        $response->assertStatus(302);
        $response->assertRedirect("products");

        $this->assertDatabaseMissing("products", $product->toArray());
        $this->assertDatabaseCount('products', 0);
    }

    public function test_api_returns_product_list()
    {
        $product = Product::factory()->create();

        $response = $this->getJson('/api/products');

        $response->assertJson([$product->toArray()]);
    }

    public function test_api_product_store_successful()
    {
        $product = [
            'name' => 'Test Product',
            'price' => 1089
        ];

        $response = $this->postJson('/api/products', $product);

        $response->assertStatus(201);
        $response->assertJson($product);
    }

    public function test_api_product_invalid_store_returns_error()
    {
        $product = [
            'name' => '',
            'price' => 1233
        ];

        $response = $this->postJson('/api/products', $product);

        $response->assertStatus(422);
    }
}
