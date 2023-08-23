<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Services\Filters\BaseFilter;
use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index(Request $request, BaseFilter $filter)
    {
        $categories = Category::filter($filter)
            ->paginate($request->get('limit', 10))
            ->withQueryString();

        return $this->success(data: $categories, onlyData: true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->toArray());

        return $this->success(['uuid' => $category->uuid]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return $this->success($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->toArray());
        return $this->success($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return $this->success([]);
    }
}
