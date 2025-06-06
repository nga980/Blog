@extends('layouts.user')

@section('content')
<div class="container py-4">
    <form method="GET" action="{{ route('user.home') }}" class="mb-4">
        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Tìm kiếm bài viết" class="form-control" />
    </form>

    <div class="d-flex flex-column gap-3 align-items-center">
        @foreach ($posts as $post)
        <div class="post-link text-decoration-none text-dark" style="width: 600px; position: relative; cursor: pointer;" onclick="window.location='{{ route('posts.show', $post) }}'">
            <div class="card rounded shadow-effect overflow-hidden position-relative">
                <a href="{{ route('categories.show', $post->category->id) }}" class="category-link text-decoration-none" style="position: absolute; top: 0; left: 0; z-index: 10; display: inline-block;">
                    <span class="m-2 badge bg-primary text-white small category-badge">
                        {{ $post->category->title ?? 'Chưa phân loại' }}
                    </span>
                </a>

                <div class="card-body d-flex flex-column align-items-center pt-4">
                    <h5 class="card-title fw-bold text-center mb-2" style="min-height: 2.5em;">{{ $post->title }}</h5>
                    <p class="card-text text-muted text-center mb-3" style="min-height: 3em;">{{ $post->short_description }}</p>

                    @if ($post->banner)
                    <div class="post-image-wrapper">
                        <img src="{{ asset('storage/' . $post->banner) }}" alt="Banner" class="img-fluid post-image">
                    </div>
                    @else
                    <div style="height: 180px; max-width: 66%; background-color: #eee;"></div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
    .shadow-effect {
        transition: all 0.4s ease;
        background-color: #fff;
        border-radius: 12px;
    }

    .shadow-effect:hover {
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
        transform: translateY(-8px) scale(1.01);
        border: 1px solid rgba(0, 123, 255, 0.2);
    }

    .post-image-wrapper {
        max-width: 66%;
        overflow: hidden;
        border-radius: 8px;
    }

    .post-image {
        transition: transform 0.4s ease;
        object-fit: contain;
    }

    

    /* Thêm style để category badge có thể nhận sự kiện chuột tốt hơn */
    .category-badge {
        cursor: pointer; /* Thêm con trỏ để người dùng biết có thể tương tác */
    }

    /* Thêm hiệu ứng gạch chân khi hover vào thẻ thể loại */
    .category-link:hover .category-badge {
        text-decoration: underline;
    }
</style>
@endsection
