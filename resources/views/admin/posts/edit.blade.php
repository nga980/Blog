@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow border-0">
        <div class="card-body">
            <h1 class="h4 mb-4 fw-bold">✏️ Chỉnh sửa bài viết</h1>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Lỗi!</strong> Vui lòng kiểm tra lại:
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $post->title) }}" placeholder="Tiêu đề" required>
                    <label for="title">Tiêu đề</label>
                </div>

                <div class="mb-3">
                    <label for="short_description" class="form-label fw-semibold">Mô tả ngắn</label>
                    <textarea class="form-control" id="short_description" name="short_description" rows="3" placeholder="Mô tả ngắn">{{ old('short_description', $post->short_description) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label fw-semibold">Nội dung</label>
                    <textarea class="form-control" id="content" name="content">{{ old('content', $post->content) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="banner" class="form-label fw-semibold">Ảnh banner</label>
                    <input class="form-control" type="file" id="banner" name="banner" accept="image/*" onchange="previewBanner(event)">
                    <div class="mt-2">
                        <img id="bannerPreview" 
                             src="{{ $post->banner ? asset('storage/' . $post->banner) : '#' }}" 
                             alt="Preview Banner" 
                             class="img-thumbnail" 
                             style="{{ $post->banner ? '' : 'display:none;' }} max-width: 300px;">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="gallery" class="form-label fw-semibold">Ảnh gallery (nhiều ảnh)</label>
                    <input class="form-control" type="file" id="gallery" name="gallery[]" accept="image/*" multiple onchange="previewGallery(event)">
                    <div id="galleryPreview" class="d-flex flex-wrap mt-2 gap-2">
                        @foreach($gallery as $image)
                            <div class="position-relative">
                                <img src="{{ asset('storage/' . $image) }}" class="rounded border" style="max-width: 100px; max-height: 80px;" alt="Gallery Image">
                                <label class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 px-1 py-0" title="Xóa ảnh">
                                    <input type="checkbox" name="remove_gallery[]" value="{{ $image }}" hidden>
                                    &times;
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('posts.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Lưu bài viết
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- CKEditor --}}
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    let editor;
    ClassicEditor
        .create(document.querySelector('#content'))
        .then(newEditor => { editor = newEditor; })
        .catch(error => { console.error(error); });

    document.querySelector('form').addEventListener('submit', function () {
        document.querySelector('#content').value = editor.getData();
    });

    function previewBanner(event) {
        const preview = document.getElementById('bannerPreview');
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    function previewGallery(event) {
        const files = event.target.files;
        const previewContainer = document.getElementById('galleryPreview');
        previewContainer.innerHTML = '';
        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.classList.add('rounded', 'border', 'me-2', 'mb-2');
                img.style.maxWidth = '100px';
                img.style.maxHeight = '80px';
                previewContainer.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    }
</script>
@endsection
