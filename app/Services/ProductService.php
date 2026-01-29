<?php

namespace App\Services;

use App\Models\Product;
use App\DTO\ProductDTO;

class ProductService
{
    public function create(ProductDTO $dto, array $images = []): Product
    {
        $product = Product::create([
            'product_name' => $dto->product_name,
            'product_price' => $dto->product_price,
            'product_description' => $dto->product_description,
            'product_images' => $images,
        ]);

        return $product;
    }

    public function update(Product $product, ProductDTO $dto, array $images = []): Product
    {
        $product->update([
            'product_name' => $dto->product_name,
            'product_price' => $dto->product_price,
            'product_description' => $dto->product_description,
            'product_images' => $images ?: $product->product_images,
        ]);

        return $product;
    }

    public function delete(Product $product): void
    {
        $product->delete(); // soft delete
    }
}
