<?php
namespace App\Services;

use App\Models\Category;
class CategoryService
{
    public function allCategory()
    {
        return response()->json(Category::all(),200);
    }

    public function test()
    {
        dd('test');
    }
}