<?php

namespace App\DTO;

use Illuminate\Http\Request;

class ProductDTO
{
    public function __construct(
        public string $product_name,
        public float $product_price,
        public ?string $product_description = null
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            product_name: $request->input('product_name'),
            product_price: (float) $request->input('product_price'),
            product_description: $request->input('product_description')
        );
    }
}
