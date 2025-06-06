@extends('layouts.user')

@section('content')
<div class="container py-4">

    {{-- Tìm kiếm danh mục --}}
    <div class="mb-4">
        <input type="text" id="categorySearch" class="form-control" placeholder="Tìm kiếm danh mục" onkeyup="filterCategories()" />
    </div>

    @php
        $parentCategories = $categories->whereNull('parent_id');
        $childCategories = $categories->whereNotNull('parent_id');
    @endphp

    {{-- Danh mục lớn --}}
    <h4 class="mb-3">Danh mục lớn</h4>
    <div class="row row-cols-2 row-cols-md-4 g-3 mb-5" id="parentCategories">
        @foreach ($parentCategories as $parent)
            <div class="col category-item">
                <a href="{{ route('categories.show', $parent->id) }}" class="card category-card h-100 text-decoration-none text-dark" title="{{ $parent->title }}">
                    <div class="row g-0">
                        <div class="col-4 d-flex align-items-center justify-content-center bg-secondary text-white" style="min-height: 120px;">
                            @if ($parent->image_path)
                                <img src="{{ asset('storage/' . $parent->image_path) }}" alt="{{ $parent->title }}" class="img-fluid rounded-start" style="max-height: 120px; object-fit: cover;">
                            @else
                                <span>Không có ảnh</span>
                            @endif
                        </div>
                        <div class="col-8 p-3 d-flex flex-column justify-content-center">
                            <h5 class="card-title mb-2">{{ $parent->title }}</h5>
                            <p class="card-text text-truncate">{{ $parent->short_description }}</p>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    {{-- Danh mục chi tiết --}}
    <h4 class="mb-3">Danh mục chi tiết</h4>
    <div class="row row-cols-2 row-cols-md-4 g-3" id="childCategories">
        @foreach ($childCategories as $child)
            <div class="col category-item">
                <a href="{{ route('categories.show', $child->id) }}" class="card category-card h-100 text-decoration-none text-dark category-box-child" title="{{ $child->title }}">
                    <div class="row g-0">
                        <div class="col-4 d-flex align-items-center justify-content-center bg-secondary text-white" style="min-height: 120px;">
                            @if ($child->image_path)
                                <img src="{{ asset('storage/' . $child->image_path) }}" alt="{{ $child->title }}" class="img-fluid rounded-start" style="max-height: 120px; object-fit: cover;">
                            @else
                                <span>Không có ảnh</span>
                            @endif
                        </div>
                        <div class="col-8 p-3 d-flex flex-column justify-content-center">
                            <h5 class="card-title mb-2">{{ $child->title }}</h5>
                            <p class="card-text text-truncate">{{ $child->short_description }}</p>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>

<script>
    function filterCategories() {
        const input = document.getElementById('categorySearch');
        const filter = input.value.toLowerCase();
        const items = document.querySelectorAll('.category-item');

        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.indexOf(filter) > -1) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }
</script>

<style>
    .category-item a {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .category-item a {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endsection
