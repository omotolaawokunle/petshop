<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\Filters\ProductFilter;

class ProductController extends Controller
{
    /**
     * Get a listing of all products.
     * @unauthenticated
     */
    public function index(Request $request, ProductFilter $filter): JsonResponse
    {
        $products = Product::filter($filter)
            ->with('category')
            ->paginate($request->get('limit', 10))
            ->withQueryString();

        return $this->success(data: $products, onlyData: true);
    }

    /**
     * Create a product.
     */
    public function store(ProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());
        return $this->success(['uuid' => $product->uuid]);
    }

    /**
     * Get an existing product.
     * @aunauthenticated
     * @param Product $product The uuid of the product
     */
    public function show(Product $product): JsonResponse
    {
        return $this->success(new ProductResource($product->load('category')->append('brand')));
    }

    /**
     * Update an existing product.
     * @param Product $product The uuid of the product
     */
    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());
        return $this->success(new ProductResource($product->load('category')->append('brand')));
    }

    /**
     * Delete an existing product.
     * @param Product $product The uuid of the product
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return $this->success([]);
    }
}
