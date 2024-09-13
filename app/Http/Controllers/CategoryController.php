<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    protected $categoryService;
    public function __contruct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }
    public function getcategory()
    {
        return response()->json(Category::all(),200);

    }
}
