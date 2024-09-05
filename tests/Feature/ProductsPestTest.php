<?php

use App\Models\Product;

beforeEach(function() {
    $this->user = createUser();
    $this->admin = createUser(isAdmin: true);
});

test('homepage contains empty table', function() {
    $this->actingAs($this->user)
    ->get('/products')
    ->assertStatus(200)
    ->assertSee(__('No products found'));
});

test('homepage contains non empty table', function () {
    $product = Product::create([
        'name' => 'test',
        'price' => 1999
    ]);

    $this->actingAs($this->user)
        ->get('/products')
        ->assertStatus(200)
        ->assertDontSee(__('No products found'))
        ->assertViewHas('products', function ($collection) use ($product) {
        return $collection->contains($product);
    });
});

test('create product successful', function() {
    $product = [
        'name' => 'Dell Laptop',
        'price' => 1000
    ];

    $this->actingAs($this->admin)
    ->post('/products/create', $product)
    ->assertRedirect('products');

    // Product exists in db
    $this->assertDatabaseHas('products', $product);

    // If Product is the latest one
    $lastProduct = Product::latest()->first();

    expect($lastProduct->name)->toBe($product['name']);
    expect($lastProduct->price)->toBe($product['price']);

    // $this->assertEquals($lastProduct->name, $product['name']);
    // $this->assertEquals($lastProduct->price, $product['price']);
});