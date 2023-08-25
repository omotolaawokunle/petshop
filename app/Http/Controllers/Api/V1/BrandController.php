<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\BrandRequest;
use App\Http\Controllers\Controller;
use App\Services\Filters\BaseFilter;
use Illuminate\Pagination\LengthAwarePaginator;

class BrandController extends Controller
{
    /**
     * Get a listing of all brands
     * @unauthenticated
     * @param  Request      $request
     * @param  BaseFilter   $filter
     * @return JsonResponse
     */
    public function index(Request $request, BaseFilter $filter): JsonResponse
    {
        $brands = Brand::filter($filter)
            ->paginate($request->get('limit', 10))
            ->withQueryString();

        return $this->success(data: $brands, onlyData: true);
    }

    /**
     * Create a new brand.
     * @param BrandRequest $request
     * @return JsonResponse
     */
    public function store(BrandRequest $request): JsonResponse
    {
        $brand = Brand::create($request->toArray());

        return $this->success(['uuid' => $brand->uuid]);
    }

    /**
     * Get a brand.
     * @unauthenticated
     * @param Brand $brand The uuid of the brand.
     * @return JsonResponse
     */
    public function show(Brand $brand): JsonResponse
    {
        return $this->success($brand);
    }

    /**
     * Update an existing brand.
     * @param BrandRequest $request
     * @param Brand $brand The uuid of the brand.
     * @return JsonResponse
     */
    public function update(BrandRequest $request, Brand $brand): JsonResponse
    {
        $brand->update($request->toArray());
        return $this->success($brand);
    }

    /**
     * Delete an existing brand.
     * @param Brand $brand The uuid of the brand.
     * @return JsonResponse
     */
    public function destroy(Brand $brand): JsonResponse
    {
        $brand->delete();
        return $this->success([]);
    }
}
