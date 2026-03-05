<?php

namespace Modules\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Catalog\DTOs\CategoryDTO;
use Modules\Catalog\Http\Resources\CategoryResource;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Http\Requests\UploadCategoryImageRequest;
use Modules\Catalog\Actions\UploadCategoryImageAction;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Cache::rememberForever('categories:tree', function () {
            return Category::with('children')->whereNull('parent_id')->where(
                'is_active',
                true
            )->orderBy('sort_order')->get();
        });

        return $this->success(CategoryResource::collection($categories));
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Category::class);

        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $dto = CategoryDTO::fromRequest($request);

        $category = Category::create([
            'name' => $dto->name,
            'description' => $dto->description,
            'parent_id' => $dto->parentId,
            'is_active' => $dto->isActive,
            'sort_order' => $dto->sortOrder,
        ]);

        Cache::forget('categories:tree');

        return $this->created(CategoryResource::make($category), 'Category created');
    }
    public function update(Request $request, Category $category): JsonResponse
    {
        $this->authorize('update', $category);
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $category->update($request->validated());

        Cache::forget('categories:tree');

        return $this->success(CategoryResource::make($category), 'Category updated');
    }

    public function destroy(Category $category): JsonResponse
    {
        $this->authorize('delete', $category);

        $category->delete();

        Cache::forget('categories:tree');

        return $this->noContent();
    }

    public function uploadImage(
        UploadCategoryImageRequest $request,
        Category $category,
        UploadCategoryImageAction $action
    ): JsonResponse {
        $category = $action->execute($category, $request->file('image'));

        return $this->success(
            CategoryResource::make($category),
            'Category image uploaded'
        );
    }
}
