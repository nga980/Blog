@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1>Tạo bài viết mới</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="title" class="form-label">Tiêu đề</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
        </div>

        <div class="mb-3">
            <label for="short_description" class="form-label">Mô tả ngắn</label>
            <textarea class="form-control" id="short_description" name="short_description" rows="3">{{ old('short_description') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Nội dung</label>
            <textarea class="form-control" id="content" name="content">{{ old('content') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="banner" class="form-label">Ảnh banner</label>
            <input class="form-control" type="file" id="banner" name="banner" accept="image/*" onchange="previewBanner(event)">
            <img id="bannerPreview" src="#" alt="Preview Banner" style="display:none; max-width: 300px; margin-top: 10px;">
        </div>

        <div class="mb-3">
            <label for="gallery" class="form-label">Ảnh gallery (nhiều ảnh)</label>
            <input class="form-control" type="file" id="gallery" name="gallery[]" accept="image/*" multiple onchange="previewGallery(event)">
            <div id="galleryPreview" class="d-flex flex-wrap mt-2"></div>
        </div>

        <button type="submit" class="btn btn-primary">Lưu</button>
        <a href="{{ route('posts.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#content'))
        .catch(error => {
            console.error(error);
        });

    function previewBanner(event) {
        const input = event.target;
        const preview = document.getElementById('bannerPreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '#';
            preview.style.display = 'none';
        }
    }

    function previewGallery(event) {
        const input = event.target;
        const previewContainer = document.getElementById('galleryPreview');
        previewContainer.innerHTML = '';
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '100px';
                    img.style.marginRight = '10px';
                    img.style.marginBottom = '10px';
                    previewContainer.appendChild(img);
                }
                reader.readAsDataURL(file);
            });
        }
    }
</script>
@endsection
