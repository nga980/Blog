@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm rounded">
        <div class="card-body p-4">
            <h2 class="mb-4">📝 Tạo bài viết mới</h2>

            @if ($errors->any())
                <div class="alert alert-danger rounded shadow-sm">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="sweetalert-confirm-nochange">
                @csrf

                <div class="mb-3">
                    <label for="category_id" class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                    <select class="form-select rounded" id="category_id" name="category_id" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach ($categories as $parentCategory)
                            <option value="{{ $parentCategory->id }}" {{ old('category_id') == $parentCategory->id ? 'selected' : '' }}>
                                {{ $parentCategory->title }}
                            </option>
                            @foreach ($parentCategory->children as $childCategory)
                                <option value="{{ $childCategory->id }}" {{ old('category_id') == $childCategory->id ? 'selected' : '' }}>
                                    &nbsp;&nbsp;-- {{ $childCategory->title }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="title" class="form-label fw-bold">Tiêu đề <span class="text-danger">*</span></label>
                    <input type="text" class="form-control rounded" id="title" name="title" value="{{ old('title') }}" required>
                </div>

                <div class="mb-3">
                    <label for="short_description" class="form-label fw-bold">Mô tả ngắn <span class="text-danger">*</span></label>
                    <textarea class="form-control rounded" id="short_description" name="short_description" rows="3">{{ old('short_description') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label fw-bold">Nội dung <span class="text-danger">*</span></label>
                    <textarea class="form-control rounded" id="content" name="content">{{ old('content') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="banner" class="form-label fw-bold">Ảnh banner <span class="text-danger">*</span></label>
                    <input class="form-control" type="file" id="banner" name="banner" accept="image/*" onchange="previewBanner(event)">
                    <img id="bannerPreview" src="#" alt="Preview Banner" class="img-thumbnail rounded mt-2" style="display:none; max-width: 300px;">
                </div>

                <div class="mb-3">
                    <label for="gallery" class="form-label fw-bold">Ảnh gallery (nhiều ảnh) <span class="text-danger">*</span></label>
                    <input class="form-control" type="file" id="gallery" name="gallery[]" accept="image/*" multiple onchange="previewGallery(event)">
                    <div id="galleryPreview" class="d-flex flex-wrap mt-2 gap-2"></div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">💾 Lưu</button>
                    <a href="{{ route('posts.index') }}" class="btn btn-secondary px-4 shadow-sm">↩️ Quay lại</a>
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

    // Detect unsaved changes and warn on page unload with SweetAlert2 modal
    (function() {
        let form = document.querySelector('form.sweetalert-confirm-nochange');
        if (!form) return;

        let initialData = {};
        Array.from(form.elements).forEach(element => {
            if (element.name && element.type !== 'file') {
                if (element.type === 'checkbox' || element.type === 'radio') {
                    initialData[element.name] = element.checked;
                } else {
                    initialData[element.name] = element.value;
                }
            }
        });

        let isChanged = false;

        function checkChanges() {
            isChanged = false;
            Array.from(form.elements).forEach(element => {
                if (element.name && element.type !== 'file') {
                    let currentValue;
                    if (element.type === 'checkbox' || element.type === 'radio') {
                        currentValue = element.checked;
                    } else {
                        currentValue = element.value;
                    }
                    if (initialData[element.name] !== currentValue) {
                        isChanged = true;
                    }
                } else if (element.type === 'file' && element.files.length > 0) {
                    isChanged = true;
                }
            });
        }

        form.addEventListener('change', checkChanges);
        form.addEventListener('input', checkChanges);

        // Intercept navigation events to show SweetAlert2 modal
        function confirmLeave(event) {
            checkChanges();
            if (!isChanged) return;

            event.preventDefault();
            event.stopPropagation();

            Swal.fire({
                title: 'Bạn có chắc muốn rời trang?',
                text: 'Bạn có các thay đổi chưa lưu. Nếu rời đi, các thay đổi sẽ bị mất.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Rời trang',
                cancelButtonText: 'Ở lại',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Remove event listeners to avoid infinite loop
                    window.removeEventListener('beforeunload', beforeUnloadHandler);
                    document.removeEventListener('click', clickHandler, true);
                    window.location.href = event.target.href || event.target.closest("a")?.href || window.location.href;
                }
            });
        }

        // Handle beforeunload event to show default browser dialog as fallback
        function beforeUnloadHandler(e) {
            checkChanges();
            if (isChanged) {
                e.preventDefault();
                e.returnValue = '';
            }
        }

        // Handle link clicks
        function clickHandler(e) {
            let target = e.target;
            while (target && target !== document) {
                if (target.tagName === 'A' && target.href) {
                    confirmLeave(e);
                    break;
                }
                target = target.parentNode;
            }
        }

        window.addEventListener('beforeunload', beforeUnloadHandler);
        document.addEventListener('click', clickHandler, true);

        // Handle form submit to disable warning
        form.addEventListener('submit', function() {
            window.removeEventListener('beforeunload', beforeUnloadHandler);
            document.removeEventListener('click', clickHandler, true);
        });
    })();

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
                    img.className = 'img-thumbnail rounded';
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
