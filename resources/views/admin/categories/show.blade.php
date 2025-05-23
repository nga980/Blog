@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm rounded p-4">
        <h2 class="mb-4">🔍 Chi tiết danh mục</h2>

        <div class="mb-3">
            <strong>Tiêu đề:</strong>
            <p>{{ $category->title }}</p>
        </div>

        <div class="mb-3">
            <strong>Danh mục cha:</strong>
            <p>{{ $category->parent ? $category->parent->title : '-' }}</p>
        </div>

        <div class="mb-3">
            <strong>Mô tả ngắn:</strong>
            <p>{{ $category->short_description ?? '-' }}</p>
        </div>

        <div class="mb-3">
            <strong>Nội dung:</strong>
            <div>{!! $category->content ?? '-' !!}</div>
        </div>

        <div class="mb-3">
            <strong>Tên tác giả:</strong>
            <p>{{ $category->author_name ?? '-' }}</p>
        </div>

        <div class="mb-3">
            <strong>Ảnh đại diện:</strong>
            @if($category->image_path)
                <img src="{{ asset('storage/' . $category->image_path) }}" alt="Ảnh đại diện" class="img-thumbnail rounded" style="max-width: 300px;">
            @else
                <p>-</p>
            @endif
        </div>

        <a href="{{ route('categories.index') }}" class="btn btn-secondary">↩️ Quay lại</a>
    </div>
</div>
@endsection
