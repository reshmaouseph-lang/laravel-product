<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\DTO\ProductDTO;
use App\Services\ProductService;
use App\Http\Resources\ProductCollection;

class ProductController extends Controller
{
    public function __construct(private ProductService $service) {}

    public function ui()
    {
        return view('product');
    }

    public function index()
    {
        $products = Product::latest()->paginate(5);
        return new ProductCollection($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string',
            'product_price' => 'required|numeric',
            'product_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $dto = ProductDTO::fromRequest($request);

        $images = [];
        if($request->hasFile('product_images')){
            foreach($request->file('product_images') as $file){
                $path = $file->store('products', 'public');
                $images[] = $path;
            }
        }

        $product = $this->service->create($dto, $images);

        return response()->json(['success' => true, 'product' => $product], 201);
    }

    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function update(Request $request, Product $product)
    {
         $request->validate([
                'product_name' => 'required|string',
                'product_price' => 'required|numeric',
                'product_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $dto = ProductDTO::fromRequest($request);

            $images = $product->product_images ?? [];

            if($request->hasFile('product_images')){
                foreach($request->file('product_images') as $file){
                    $path = $file->store('products', 'public');
                    $images[] = $path;
                }
            }

            $this->service->update($product, $dto, $images);

            return response()->json(['success' => true]);
    }

    public function destroy(Product $product)
    {
        $this->service->delete($product);
        return response()->json(['success' => true]);
    }
}
