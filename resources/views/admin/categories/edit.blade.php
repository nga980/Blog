@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm rounded">
        <div class="card-body p-4">
            <h2 class="mb-4">✏️ Chỉnh sửa danh mục</h2>

            @if ($errors->any())
                <div class="alert alert-danger rounded shadow-sm">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="sweetalert-confirm-nochange">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="parent_id" class="form-label fw-bold">Danh mục cha</label>
                    <select class="form-select" id="parent_id" name="parent_id">
                        <option value="">-- Không chọn --</option>
                        @foreach($parentCategories as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                {{ $parent->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="title" class="form-label fw-bold">Tiêu đề</label>
                    <input type="text" class="form-control rounded" id="title" name="title" value="{{ old('title', $category->title) }}" required maxlength="255">
                </div>

                <div class="mb-3">
                    <label for="short_description" class="form-label fw-bold">Mô tả ngắn</label>
                    <textarea class="form-control rounded" id="short_description" name="short_description" rows="3" maxlength="500">{{ old('short_description', $category->short_description) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label fw-bold">Nội dung</label>
                    <textarea class="form-control rounded" id="content" name="content">{{ old('content', $category->content) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="author_name" class="form-label fw-bold">Tên tác giả</label>
                    <input type="text" class="form-control rounded" id="author_name" name="author_name" value="{{ old('author_name', $category->author_name) }}" maxlength="255">
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label fw-bold">Ảnh đại diện</label>
                    <input class="form-control" type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)">
                    @if($category->image_path)
                        <div class="mt-2 position-relative d-inline-block">
                            <img id="imagePreview" src="{{ asset('storage/' . $category->image_path) }}" alt="Ảnh đại diện" class="img-thumbnail rounded" style="max-width: 300px;">
                            <label class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 px-1 py-0" title="Xóa ảnh">
                                <input type="checkbox" name="remove_image" value="1" hidden>
                                &times;
                            </label>
                        </div>
                    @else
                        <img id="imagePreview" src="#" alt="Preview Image" class="img-thumbnail rounded mt-2" style="display:none; max-width: 300px;">
                    @endif
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">💾 Lưu</button>
                    <a href="{{ route('categories.index') }}" class="btn btn-secondary px-4 shadow-sm">↩️ Quay lại</a>
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
        } else if (!preview.src || preview.src === window.location.href) {
            preview.style.display = 'none';
        }
    }
</script>
@endsection
