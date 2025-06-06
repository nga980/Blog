<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories for normal users.
     */
    public function userIndex()
    {
        $categories = Category::with('parent')->orderBy('parent_id')->orderBy('title')->get();

        return view('user.categories.index', compact('categories'));
    }

    /**
     * Display the specified category for normal users.
     */
    public function userShow(Category $category)
    {
        $category->load(['children', 'posts']);
        return view('user.categories.show', compact('category'));
    }
}
