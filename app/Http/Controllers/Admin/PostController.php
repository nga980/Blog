<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the posts for admin.
     */
    public function index(Request $request)
    {
        $perPage = 10;

        $categories = Category::with('children')->whereNull('parent_id')->get();

        $parentCategoryId = $request->input('parent_category_id');
        $childCategoryId = $request->input('child_category_id');

        $query = Post::query();

        if ($childCategoryId) {
            $query->where('category_id', $childCategoryId);
        } elseif ($parentCategoryId) {
            $childIds = Category::where('parent_id', $parentCategoryId)->pluck('id')->toArray();
            $categoryIds = array_merge([$parentCategoryId], $childIds);
            $query->whereIn('category_id', $categoryIds);
        }

        $posts = $query->latest()->get();

        return view('admin.posts.index', compact('posts', 'perPage', 'categories', 'parentCategoryId', 'childCategoryId'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        $categories = Category::with('children')->whereNull('parent_id')->get();
        return view('admin.posts.create', compact('categories'));
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'banner' => 'required|image|max:2048',
            'gallery' => 'required|array|min:2|max:5',
            'gallery.*' => 'required|image|max:2048',
        ]);

        $data = $request->only(['title', 'short_description', 'content', 'category_id']);
        $data['user_id'] = auth()->id();
        $data['author_name'] = auth()->user()->name ?? null;

        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('banners', 'public');
            $data['banner'] = $bannerPath;
        }

        $post = Post::create($data);

        if ($request->hasFile('gallery')) {
            $galleryPaths = [];
            foreach ($request->file('gallery') as $image) {
                $galleryPaths[] = $image->store('gallery', 'public');
            }
            $post->gallery = json_encode($galleryPaths);
            $post->save();
        }

        $queryParams = $request->only(['parent_category_id', 'child_category_id']);

        return redirect()->route('admin.posts.index', $queryParams)->with('swal_success', 'Bài viết đã được tạo thành công.');
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post)
    {
        $post->load('category');
        $gallery = $post->gallery ? json_decode($post->gallery, true) : [];
        return view('admin.posts.show', compact('post', 'gallery'));
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post)
    {
        $gallery = $post->gallery ? json_decode($post->gallery, true) : [];
        $categories = Category::with('children')->whereNull('parent_id')->get();
        return view('admin.posts.edit', compact('post', 'gallery', 'categories'));
    }

    /**
     * Update the specified post in storage.
     */
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'banner' => 'nullable|image|max:2048',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|max:2048',
            'remove_gallery' => 'nullable|array',
        ]);

        $data = $request->only(['title', 'short_description', 'content']);
        $data['user_id'] = auth()->id();
        $data['author_name'] = auth()->user()->name ?? null;

        if ($request->hasFile('banner')) {
            if ($post->banner) {
                Storage::disk('public')->delete($post->banner);
            }
            $bannerPath = $request->file('banner')->store('banners', 'public');
            $data['banner'] = $bannerPath;
        } else {
            $data['banner'] = $post->banner;
        }

        $gallery = $post->gallery ? json_decode($post->gallery, true) : [];
        if ($request->has('remove_gallery')) {
            foreach ($request->remove_gallery as $removeImage) {
                if (($key = array_search($removeImage, $gallery)) !== false) {
                    Storage::disk('public')->delete($removeImage);
                    unset($gallery[$key]);
                }
            }
            $gallery = array_values($gallery);
        }

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                $gallery[] = $image->store('gallery', 'public');
            }
        }

        $galleryCount = count($gallery);
        if ($galleryCount < 2 || $galleryCount > 5) {
            return redirect()->back()
                ->withInput()
                ->with('swal_error', 'Gallery phải có ít nhất 2 ảnh và nhiều nhất 5 ảnh.');
        }

        $data['gallery'] = json_encode(array_values($gallery));

        if (empty($data['banner'])) {
            return redirect()->back()
                ->withInput()
                ->with('swal_error', 'Banner phải có 1 ảnh.');
        }

        $post->update($data);

        $queryParams = $request->only(['parent_category_id', 'child_category_id']);

        return redirect()->route('admin.posts.index', $queryParams)->with('swal_success', 'Bài viết đã được cập nhật thành công.');
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post)
    {
        if ($post->banner) {
            Storage::disk('public')->delete($post->banner);
        }
        $gallery = $post->gallery ? json_decode($post->gallery, true) : [];
        foreach ($gallery as $image) {
            Storage::disk('public')->delete($image);
        }
        $post->delete();

        return redirect()->route('admin.posts.index')->with('swal_success', 'Bài viết đã được xóa thành công.');
    }
}
