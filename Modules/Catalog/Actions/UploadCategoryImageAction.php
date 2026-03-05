<?php

namespace Modules\Catalog\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Modules\Catalog\Models\Category;

class UploadCategoryImageAction
{
    public function execute(Category $category, UploadedFile $file): Category
    {
        // Delete old image if exists
        if ($category->image_path) {
            Storage::disk('public')->delete($category->image_path);
        }

        $path = $file->store("categories/{$category->id}", 'public');

        $category->update(['image_path' => $path]);

        Cache::forget('categories:tree');

        return $category->fresh();
    }
}
