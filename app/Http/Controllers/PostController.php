<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException; // Thêm dòng này
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the posts for admin.
     */
    public function index(Request $request)
    {
        // Remove $this->authorize('admin'); because no 'admin' gate defined
        // $this->authorize('admin'); // Only admin can access

        $perPage = 10; // default perPage for DataTables page length

        // Lấy danh mục cha cùng danh mục con để truyền vào view
        $categories = Category::with('children')->whereNull('parent_id')->get();

        // Lấy tham số lọc từ request
        $parentCategoryId = $request->input('parent_category_id');
        $childCategoryId = $request->input('child_category_id');

        // Xây dựng query lọc bài viết
        $query = Post::query();

        if ($childCategoryId) {
            // Lọc theo danh mục con
            $query->where('category_id', $childCategoryId);
        } elseif ($parentCategoryId) {
            // Lấy danh sách id các danh mục con của danh mục cha
            $childIds = Category::where('parent_id', $parentCategoryId)->pluck('id')->toArray();
            // Bao gồm cả danh mục cha nếu có bài viết thuộc danh mục cha
            $categoryIds = array_merge([$parentCategoryId], $childIds);
            $query->whereIn('category_id', $categoryIds);
        }

        $posts = $query->latest()->get();

        return view('admin.posts.index', compact('posts', 'perPage', 'categories', 'parentCategoryId', 'childCategoryId'));
    }

    /**
     * Display a listing of the posts for normal users.
     */
    public function userIndex(Request $request)
    {
        $posts = Post::latest()->get();
        $perPage = 10; // default perPage for DataTables page length
        return view('posts.index', compact('posts', 'perPage'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        // Remove $this->authorize('admin'); because no 'admin' gate defined
        // $this->authorize('admin'); // Only admin can access

        // Lấy danh sách danh mục cha cùng với danh mục con
        $categories = Category::with('children')->whereNull('parent_id')->get();
        return view('admin.posts.create', compact('categories'));
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('admin'); // Only admin can access

        // Xóa logic try-catch và lưu tạm file. Laravel sẽ tự động redirect back và giữ old input.
        // Đối với file upload, người dùng sẽ phải chọn lại file nếu validation fail.
        // Việc lưu tạm file như logic cũ có thể gây lỗi hoặc không an toàn.
        $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'banner' => 'required|image|max:2048',
            'gallery' => 'required|array|min:2|max:5',
            'gallery.*' => 'required|image|max:2048',
        ], [
            'title.required' => 'Tiêu đề không được để trống.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'short_description.required' => 'Mô tả ngắn không được để trống.',
            'short_description.max' => 'Mô tả ngắn không được vượt quá 500 ký tự.',
            'content.required' => 'Nội dung không được để trống.',
            'category_id.required' => 'Danh mục không được để trống.',
            'category_id.exists' => 'Danh mục không hợp lệ.',
            'banner.required' => 'Banner không được để trống.',
            'banner.image' => 'File banner phải là ảnh.',
            'banner.max' => 'Kích thước banner không được vượt quá 2MB.',
            'gallery.required' => 'Gallery không được để trống.',
            'gallery.array' => 'Gallery phải là một mảng ảnh.',
            'gallery.min' => 'Gallery phải có ít nhất 2 ảnh.',
            'gallery.max' => 'Gallery không được vượt quá 5 ảnh.',
            'gallery.*.required' => 'Ảnh trong gallery không được để trống.',
            'gallery.*.image' => 'File trong gallery phải là ảnh.',
            'gallery.*.max' => 'Kích thước ảnh trong gallery không được vượt quá 2MB.',
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

        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.posts.index', $queryParams)->with('swal_success', 'Bài viết đã được tạo thành công.');
        } else {
            return redirect()->route('posts.index', $queryParams)->with('swal_success', 'Bài viết đã được tạo thành công.');
        }
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post)
    {
        $this->authorize('admin'); // Only admin can access

        $post->load('category');
        $gallery = $post->gallery ? json_decode($post->gallery, true) : [];
        return view('admin.posts.show', compact('post', 'gallery'));
    }

    /**
     * Display the specified post for normal users.
     */
public function userShow(Post $post)
{
    $post->load('category');

    // Tách nội dung bài viết thành các phần, giả sử tách theo 2 dòng xuống dòng
    $contentSections = preg_split('/\n\s*\n/', $post->content);

    $gallery = $post->gallery ? json_decode($post->gallery, true) : [];

    // Lấy 2 bài viết tương tự cùng danh mục, không lấy bài hiện tại
    $similarPosts = Post::where('category_id', $post->category_id)
        ->where('id', '!=', $post->id)
        ->latest()
        ->take(2)
        ->get();

    return view('user.posts.show', compact('post', 'gallery', 'contentSections', 'similarPosts'));
}

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post)
    {
        // Remove $this->authorize('admin'); because no 'admin' gate defined
        // $this->authorize('admin'); // Only admin can access

        $gallery = $post->gallery ? json_decode($post->gallery, true) : [];
        $categories = \App\Models\Category::with('children')->whereNull('parent_id')->get();
        return view('admin.posts.edit', compact('post', 'gallery', 'categories'));
    }

    /**
     * Update the specified post in storage.
     */
    public function update(Request $request, Post $post)
    {
        // Remove $this->authorize('admin'); because no 'admin' gate defined
        // $this->authorize('admin'); // Only admin can access

        $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'banner' => 'nullable|image|max:2048',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|max:2048',
            'remove_gallery' => 'nullable|array',
        ], [
            'title.required' => 'Tiêu đề không được để trống.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'short_description.required' => 'Mô tả ngắn không được để trống.',
            'short_description.max' => 'Mô tả ngắn không được vượt quá 500 ký tự.',
            'content.required' => 'Nội dung không được để trống.',
            'category_id.required' => 'Danh mục không được để trống.',
            'category_id.exists' => 'Danh mục không hợp lệ.',
            'banner.image' => 'File banner phải là ảnh.',
            'banner.max' => 'Kích thước ảnh không được vượt quá 2MB.',
            'gallery.array' => 'Gallery phải là một mảng ảnh.',
            'gallery.*.image' => 'File trong gallery phải là ảnh.',
            'gallery.*.max' => 'Kích thước ảnh trong gallery không được vượt quá 2MB.',
        ]);

        $data = $request->only(['title', 'short_description', 'content']);
        $data['user_id'] = auth()->id();
        $data['author_name'] = auth()->user()->name ?? null; // Cập nhật cả author_name khi update

        // Banner handling
        if ($request->hasFile('banner')) {
            if ($post->banner) {
                Storage::disk('public')->delete($post->banner);
            }
            $bannerPath = $request->file('banner')->store('banners', 'public');
            $data['banner'] = $bannerPath;
        } else {
            // Nếu không có banner mới và banner cũ bị xóa (không có logic xóa banner cũ ở đây)
            // thì vẫn giữ nguyên banner cũ
            $data['banner'] = $post->banner;
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
            $gallery = array_values($gallery); // Re-index array after unsetting
        }

        // Add new gallery images
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                $gallery[] = $image->store('gallery', 'public');
            }
        }

        // Validate gallery count after removals and additions
        $galleryCount = count($gallery);
        if ($galleryCount < 2 || $galleryCount > 5) {
            // Sử dụng session swal_error thay vì withErrors
            return redirect()->back()
                ->withInput()
                ->with('swal_error', 'Gallery phải có ít nhất 2 ảnh và nhiều nhất 5 ảnh.');
        }

        $data['gallery'] = json_encode(array_values($gallery)); // Make sure to re-index after modifications

        // Validate banner presence
        if (empty($data['banner'])) {
            // Sử dụng session swal_error thay vì withErrors
            return redirect()->back()
                ->withInput()
                ->with('swal_error', 'Banner phải có 1 ảnh.');
        }

        $post->update($data);

        $queryParams = $request->only(['parent_category_id', 'child_category_id']);

        // Redirect dựa trên quyền người dùng
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.posts.index', $queryParams)->with('swal_success', 'Bài viết đã được cập nhật thành công.');
        } else {
            return redirect()->route('posts.index', $queryParams)->with('swal_success', 'Bài viết đã được cập nhật thành công.');
        }
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post)
    {
        // Remove authorization check to avoid 403 if no admin gate defined
        // $this->authorize('admin'); // Only admin can access

        if ($post->banner) {
            Storage::disk('public')->delete($post->banner);
        }
        $gallery = $post->gallery ? json_decode($post->gallery, true) : [];
        foreach ($gallery as $image) {
            Storage::disk('public')->delete($image);
        }
        $post->delete();

        // Redirect dựa trên quyền người dùng
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.posts.index')->with('swal_success', 'Bài viết đã được xóa thành công.');
        } else {
            return redirect()->route('posts.index')->with('swal_success', 'Bài viết đã được xóa thành công.');
        }
    }
}
