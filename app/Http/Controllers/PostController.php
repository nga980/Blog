<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the posts.
     */
    public function index(Request $request)
    {
        $posts = Post::latest()->get();
        $perPage = 10; // default perPage for DataTables page length
        return view('admin.posts.index', compact('posts', 'perPage'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        return view('admin.posts.create');
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'banner' => 'nullable|image|max:2048',
            'gallery.*' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['title', 'short_description', 'content']);
        $data['user_id'] = auth()->id();
        $data['author_name'] = auth()->user()->name ?? null;
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

        return redirect()->route('posts.index')->with('success', 'Bài viết đã được tạo thành công.');
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post)
    {
        $gallery = $post->gallery ? json_decode($post->gallery, true) : [];
        return view('admin.posts.show', compact('post', 'gallery'));
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post)
    {
        $gallery = $post->gallery ? json_decode($post->gallery, true) : [];
        return view('admin.posts.edit', compact('post', 'gallery'));
    }

    /**
     * Update the specified post in storage.
     */
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'banner' => 'nullable|image|max:2048',
            'gallery.*' => 'nullable|image|max:2048',
            'remove_gallery' => 'nullable|array',
        ]);

        $data = $request->only(['title', 'short_description', 'content']);
        $data['user_id'] = auth()->id();

        if ($request->hasFile('banner')) {
            if ($post->banner) {
                Storage::disk('public')->delete($post->banner);
            }
            $bannerPath = $request->file('banner')->store('banners', 'public');
            $data['banner'] = $bannerPath;
        }

        // Remove selected gallery images
        $gallery = $post->gallery ? json_decode($post->gallery, true) : [];
        if ($request->has('remove_gallery')) {
            foreach ($request->remove_gallery as $removeImage) {
                if (($key = array_search($removeImage, $gallery)) !== false) {
                    Storage::disk('public')->delete($removeImage);
                    unset($gallery[$key]);
                }
            }
        }

        // Add new gallery images
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                $gallery[] = $image->store('gallery', 'public');
            }
        }

        $data['gallery'] = json_encode(array_values($gallery));

        $post->update($data);

        return redirect()->route('posts.index')->with('success', 'Bài viết đã được cập nhật thành công.');
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

        return redirect()->route('posts.index')->with('success', 'Bài viết đã được xóa thành công.');
    }
}
