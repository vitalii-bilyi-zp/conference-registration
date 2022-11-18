<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\Category\Index as CategoryIndex;
use App\Http\Requests\Category\Store as CategoryStore;
use App\Http\Requests\Category\Show as CategoryShow;
use App\Http\Requests\Category\Update as CategoryUpdate;
use App\Http\Requests\Category\Destroy as CategoryDestroy;

use App\Models\Category;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

use App\Services\CategoryService;

class CategoryController extends Controller
{
    use ApiResponseHelpers;

    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(CategoryIndex $request): JsonResponse
    {
        $categories = Category::with('subcategories')
            ->whereNull('category_id')
            ->get();
        $categories = $categories->transform(function ($value) {
            return $this->categoryService->appendSubcategoriesRecursively($value);
        });

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($categories);
    }

    public function store(CategoryStore $request): JsonResponse
    {
        Category::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
        ]);

        return $this->respondWithSuccess();
    }

    public function show(CategoryShow $request, Category $category): JsonResponse
    {
        $this->categoryService->appendSubcategoriesRecursively($category);

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($category);
    }

    public function update(CategoryUpdate $request, Category $category): JsonResponse
    {
        $category->update([
            'name' => $request->name ?? $category->name,
            'category_id' => $request->category_id ?? $category->category_id,
        ]);

        return $this->respondWithSuccess();
    }

    public function destroy(CategoryDestroy $request, Category $category): JsonResponse
    {
        $category->delete();

        return $this->respondWithSuccess();
    }
}
