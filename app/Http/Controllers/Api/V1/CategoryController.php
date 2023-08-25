<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Filters\BaseFilter;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{
    /**
     * Get a listing of categories
     * @unauthenticated
     */
    public function index(Request $request, BaseFilter $filter): JsonResponse
    {
        $categories = Category::filter($filter)
            ->paginate($request->get('limit', 10))
            ->withQueryString();

        return $this->success(data: $categories, onlyData: true);
    }

    /**
     * Create a new category.
     */
    public function store(CategoryRequest $request): JsonResponse
    {
        $category = Category::create($request->toArray());

        return $this->success(['uuid' => $category->uuid]);
    }

    /**
     * Get an existing category.
     * @unauthenticated
     * @param Category $category The uuid of the category.
     */
    public function show(Category $category): JsonResponse
    {
        return $this->success($category);
    }

    /**
     * Update an existing category.
     * @param Category $category The uuid of the category.
     */
    public function update(CategoryRequest $request, Category $category): JsonResponse
    {
        $category->update($request->toArray());
        return $this->success($category);
    }

    /**
     * Delete an existing category.
     * @param Category $category The uuid of the category.
     */
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();
        return $this->success([]);
    }
}
