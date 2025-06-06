@extends('layouts.user')

@section('content')
<div class="container py-4">
    {{-- Nút Quay lại Trang chủ --}}
    <div class="mb-4">
        <a href="{{ route('user.home') }}" class="btn btn-outline-secondary">
            {{-- Bạn có thể thêm icon nếu sử dụng thư viện icon như Font Awesome --}}
            {{-- <i class="fas fa-arrow-left"></i> --}}
            ← Quay lại Trang chủ
        </a>
    </div>

    {{-- Thông tin chính của bài viết - Giữ nguyên bố cục cột gốc --}}
    <div class="main-post-info mb-5">
        @if($post->banner)
            {{-- Banner có thể đặt ở đây, phía trên phần thông tin cột --}}
            <div class="mb-4">
                <img src="{{ asset('storage/' . $post->banner) }}" class="img-fluid rounded shadow-sm" alt="Banner {{ $post->title }}" style="object-fit: cover; width: 100%; max-height: 450px;">
            </div>
        @endif

        <div class="row">
            <div class="col-md-4">
                <h1 class="mb-3" style="font-size: 2.25rem; font-weight: 500;">{{ $post->title }}</h1>
                <p class="text-muted">
                    <strong>Thể loại:</strong>
                    <a href="{{ route('categories.show', $post->category->id) }}" class="text-decoration-none">
                        <span class="badge bg-primary fs-6 ms-1 category-badge">
                            {{ $post->category->title ?? 'Chưa phân loại' }}
                        </span>
                    </a>
                </p>
                @if($post->created_at)
                <p class="text-muted small"><em>Đăng lúc: {{ $post->created_at->format('H:i, d/m/Y') }}</em></p>
                @endif
            </div>
            <div class="col-md-8">
                @if($post->author_name)
                    <p class="text-muted mb-1"><strong>Tác giả:</strong> {{ $post->author_name }}</p>
                @endif
                @if($post->short_description)
                    <p class="lead" style="font-size: 1.1rem;"><strong>Mô tả ngắn:</strong> {{ $post->short_description }}</p>
                @else
                    <p class="lead fst-italic" style="font-size: 1.1rem;"><strong>Mô tả ngắn:</strong> (Không có)</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Đường kẻ phân cách --}}
    <hr class="my-4">

    {{-- Nội dung bài viết và thư viện ảnh --}}
    <div class="content-sections">
        @php
            $contentCount = count($contentSections ?? []);
            $galleryCount = count($gallery ?? []);
            $maxItems = max($contentCount, $galleryCount);
        @endphp

        @for ($i = 0; $i < $maxItems; $i++)
            @if (isset($contentSections[$i]))
                <div class="content-block mb-4 p-3 bg-white rounded shadow-sm">
                    @php
                        // Giữ nguyên logic xử lý $contentWithoutP theo yêu cầu
                        $contentWithoutP = str_replace(['<p>', '</p>'], '', $contentSections[$i]);
                    @endphp
                    {!! $contentWithoutP !!}
                </div>
            @endif

            @if (isset($gallery[$i]))
                <div class="gallery-block mb-4 text-center">
                    <img src="{{ asset('storage/' . $gallery[$i]) }}" alt="Gallery Image {{ $i + 1 }}" class="img-fluid rounded shadow-sm" style="max-height: 600px; display: inline-block;">
                </div>
            @endif
        @endfor
    </div>

    {{-- Bài viết tương tự --}}
    @if ($similarPosts && $similarPosts->count() > 0)
        <div class="mt-5 pt-4 border-top">
            <h3 class="mb-4">Gợi ý bài viết tương tự</h3>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach ($similarPosts as $similar)
                    <div class="col d-flex align-items-stretch">
                        <a href="{{ route('posts.show', $similar->id) }}" class="card h-100 shadow-sm text-decoration-none link-dark similar-post-card">
                            @if ($similar->banner)
                                <img src="{{ asset('storage/' . $similar->banner) }}" class="card-img-top" alt="{{ $similar->title }}" style="height: 200px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <span class="text-muted">Không có ảnh</span>
                                </div>
                            @endif
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title mb-2">{{ Str::limit($similar->title, 60) }}</h5>
                                <p class="card-text small text-muted flex-grow-1">{{ Str::limit($similar->short_description, 120) }}</p>
                                @if($similar->created_at)
                                <p class="card-text mb-0"><small class="text-muted">{{ $similar->created_at->diffForHumans() }}</small></p>
                                @endif
                            </div>
                            <div class="card-footer bg-transparent border-top-0 text-end pb-3">
                                <span class="btn btn-sm btn-outline-primary">Xem chi tiết</span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

{{-- CSS tùy chỉnh (nếu cần) --}}
<style>
    .main-post-info {
        padding: 1.5rem;
        background-color: #f8f9fa; /* Một màu nền nhẹ cho khu vực thông tin chính */
        border-radius: 0.3rem;
        border: 1px solid #dee2e6;
    }
    .content-block {
        line-height: 1.75;
        font-size: 1.05rem; /* Điều chỉnh lại một chút cho hài hòa */
    }
    .content-block p:last-child {
        margin-bottom: 0;
    }
    .content-block img {
        max-width: 100%;
        height: auto;
        border-radius: 0.3rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        margin: 1rem 0;
    }
    .similar-post-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .similar-post-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
    }
    .badge.bg-primary {
        padding: 0.4em 0.65em;
        font-weight: 500;
    }
</style>
@endsection