<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function appendSubcategoriesRecursively(Category $category)
    {
        $category->subcategories->each(function ($item) {
            $this->appendSubcategoriesRecursively($item);
        });

        return $category;
    }

    public function getSubcategoryIdsRecursively(Category $category)
    {
        $ids = [$category->id];

        $category->subcategories->each(function ($item) use (&$ids) {
            $ids = array_merge($ids, $this->getSubcategoryIdsRecursively($item));
        });

        return $ids;
    }
}
