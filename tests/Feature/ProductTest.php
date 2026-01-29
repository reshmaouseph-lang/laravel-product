<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test product creation
     */
    public function test_product_can_be_created()
    {
        $payload = [
            'product_name' => 'Laptop',
            'product_price' => 45000,
            'product_description' => 'Gaming Laptop',
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('products', [
            'product_name' => 'Laptop',
            'product_price' => 45000
        ]);
    }

    /**
     * Test product listing with pagination
     */
    public function test_products_are_paginated()
    {
        Product::factory()->count(15)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data',
                     'links',
                     'meta'
                 ]);

        // Default pagination 5 per page
        $this->assertCount(5, $response->json('data'));
    }

    /**
     * Test product update
     */
    public function test_product_can_be_updated()
    {
        $product = Product::factory()->create();

        $payload = [
            'product_name' => 'Updated Name',
            'product_price' => 999,
            'product_description' => 'Updated description',
        ];

        $response = $this->putJson("/api/products/{$product->id}", $payload);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'product_name' => 'Updated Name',
            'product_price' => 999
        ]);
    }

    /**
     * Test soft delete product
     */
    public function test_product_is_soft_deleted()
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");
        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        // Check soft delete
        $this->assertSoftDeleted('products', [
            'id' => $product->id
        ]);

        // Ensure product is excluded from default listing
        $res = $this->getJson('/api/products');
        $ids = collect($res->json('data'))->pluck('id');
        $this->assertFalse($ids->contains($product->id));
    }

    /**
     * Test validation failure
     */
    public function test_validation_fails_without_product_name()
    {
        $payload = [
            'product_price' => 1000
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['product_name']);
    }
}
