@extends('layouts.user')

@section('content')
<div class="container py-4 text-center">
    <h1 class="mb-4 fw-bold">{{ $category->title }}</h1>

    @if ($category->image_path)
        <img src="{{ asset('storage/' . $category->image_path) }}" alt="{{ $category->title }}" class="img-fluid mb-3 mx-auto" style="max-width: 400px;">
    @endif

    @if ($category->short_description)
        <p class="lead">{{ $category->short_description }}</p>
    @endif

    <div class="mb-4 text-start">
        {!! $category->content !!}
    </div>

    @if ($category->children && $category->children->count() > 0)
        <div class="mb-4 text-start">
            <h4>Danh mục con</h4>
            <ul class="list-group">
                @foreach ($category->children as $child)
                    <li class="list-group-item">
                        <a href="{{ route('categories.show', $child->id) }}">{{ $child->title }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($category->posts && $category->posts->count() > 0)
        <div class="mb-4 text-start">
            <h4>Bài viết thuộc danh mục</h4>
            <div class="list-group">
                @foreach ($category->posts as $post)
                    <a href="{{ route('admin.posts.show', $post->id) }}" class="list-group-item list-group-item-action d-flex align-items-center">
                        @if ($post->banner)
                            <img src="{{ asset('storage/' . $post->banner) }}" alt="{{ $post->title }}" class="me-3" style="width: 100px; height: 70px; object-fit: cover; border-radius: 0.25rem;">
                        @else
                            <div class="me-3 bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 100px; height: 70px; border-radius: 0.25rem;">
                                Không có ảnh
                            </div>
                        @endif
                        <div>
                            <h5 class="mb-1">{{ $post->title }}</h5>
                            <p class="mb-0 text-truncate" style="max-width: 600px;">{{ $post->short_description }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <a href="{{ route('categories.index') }}" class="btn btn-secondary mt-3">Quay lại danh sách danh mục</a>
</div>
@endsection
