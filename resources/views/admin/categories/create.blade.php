@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm rounded">
        <div class="card-body p-4">
            <h2 class="mb-4">📂 Tạo danh mục mới</h2>

            @if ($errors->any())
                <div class="alert alert-danger rounded shadow-sm">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="sweetalert-confirm-nochange">
                @csrf

                <div class="mb-3">
                    <label for="parent_id" class="form-label fw-bold">Danh mục cha</label>
                    <select class="form-select" id="parent_id" name="parent_id">
                        <option value="">-- Không chọn --</option>
                        @foreach($parentCategories as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="title" class="form-label fw-bold d-flex align-items-center gap-1">
                        Tiêu đề <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control rounded" id="title" name="title" value="{{ old('title') }}" required maxlength="255">
                </div>

                <div class="mb-3">
                    <label for="short_description" class="form-label fw-bold">Mô tả ngắn</label>
                    <textarea class="form-control rounded" id="short_description" name="short_description" rows="3" maxlength="500">{{ old('short_description') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label fw-bold">Nội dung</label>
                    <textarea class="form-control rounded" id="content" name="content">{{ old('content') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="author_name" class="form-label fw-bold">Tên tác giả</label>
                    <input type="text" class="form-control rounded" id="author_name" name="author_name" value="{{ old('author_name') }}" maxlength="255">
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label fw-bold">Ảnh đại diện</label>
                    <input class="form-control" type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)">
                    <img id="imagePreview" src="#" alt="Preview Image" class="img-thumbnail rounded mt-2" style="display:none; max-width: 300px;">
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">💾 Lưu</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary px-4 shadow-sm">↩️ Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>

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

    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('imagePreview');
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
</script>
@endsection
