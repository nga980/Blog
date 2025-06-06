<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class UserHomeController extends Controller
{
    /**
     * Display the user homepage with posts list and search.
     */
    public function index(Request $request)
    {
        $query = Post::query();

        $search = null;
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
        }

        $posts = $query->latest()->get();

        return view('user.home', compact('posts', 'search'));
    }
}
