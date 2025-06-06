@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-body p-4">
            <h1 class="h4 mb-4 fw-bold text-primary">📄 Xem bài viết</h1>

            <div class="mb-4">
                <label class="form-label fw-semibold text-muted">📂 Danh mục</label>
                <div class="p-3 bg-light border rounded">
                    {{ $post->category ? $post->category->title : 'Chưa có danh mục' }}
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold text-muted">📝 Tiêu đề</label>
                <div class="p-3 bg-light border rounded">{{ $post->title }}</div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold text-muted">📌 Mô tả ngắn</label>
                <div class="p-3 bg-light border rounded">{!! nl2br(e($post->short_description)) !!}</div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold text-muted">📖 Nội dung</label>
                <div class="border rounded p-4 bg-white shadow-sm" style="min-height: 200px; white-space: pre-wrap;">
                    {!! $post->content !!}
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold text-muted">🖼️ Ảnh banner</label><br>
                @if($post->banner)
                    <img src="{{ asset('storage/' . $post->banner) }}" alt="Banner" class="img-fluid rounded-3 shadow-sm border" style="max-width: 100%;">
                @else
                    <div class="text-muted fst-italic">Không có ảnh banner</div>
                @endif
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold text-muted">🖼️ Ảnh gallery</label><br>
                @if($gallery && count($gallery) > 0)
                    <div class="d-flex flex-wrap gap-3">
                        @foreach($gallery as $image)
                            <img src="{{ asset('storage/' . $image) }}" alt="Gallery Image" class="rounded shadow-sm border" style="width: 100px; height: auto;">
                        @endforeach
                    </div>
                @else
                    <div class="text-muted fst-italic">Không có ảnh gallery</div>
                @endif
            </div>

            <div class="text-end">
                <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary btn-sm">
                    ⬅ Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
