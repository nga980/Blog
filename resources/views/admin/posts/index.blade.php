@extends('layouts.app')

@section('content')
<div class="py-4 font-sans">
    <div class="container">
        <div class="card shadow rounded-4 border-0">
            <div class="card-header d-flex justify-content-between align-items-center text-white rounded-top-4 px-4 py-3" style="background: linear-gradient(to right, #007bff, #0056b3);">
                <h4 class="mb-0 fw-semibold"><i class="fa fa-list-alt me-2"></i>Danh sách bài viết</h4>
                <a href="{{ route('admin.posts.create') }}" class="btn btn-success btn-sm rounded-pill shadow-sm">
                    <i class="fa fa-plus me-1"></i> Thêm bài viết
                </a>
            </div>
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-pill text-center fw-medium d-flex align-items-center justify-content-center gap-2" role="alert">
                        <i class="fa fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <form id="filterForm" method="GET" class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <label for="parentCategorySelect" class="form-label mb-0 fw-semibold">Danh mục cha:</label>
                            <select id="parentCategorySelect" name="parent_category_id" class="form-select form-select-sm" style="width: auto;">
                                <option value="">-- Tất cả --</option>
                                @foreach ($categories as $parentCategory)
                                    <option value="{{ $parentCategory->id }}" {{ (string) $parentCategoryId === (string) $parentCategory->id ? 'selected' : '' }}>
                                        {{ $parentCategory->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label for="childCategorySelect" class="form-label mb-0 fw-semibold">Danh mục con:</label>
                            <select id="childCategorySelect" name="child_category_id" class="form-select form-select-sm" style="width: auto;">
                                <option value="">-- Tất cả --</option>
                                @if ($parentCategoryId)
                                    @php
                                        $childCategories = $categories->firstWhere('id', $parentCategoryId)->children ?? collect();
                                    @endphp
                                    @foreach ($childCategories as $childCategory)
                                        <option value="{{ $childCategory->id }}" {{ (string) $childCategoryId === (string) $childCategory->id ? 'selected' : '' }}>
                                            {{ $childCategory->title }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label for="perPageSelect" class="form-label mb-0 fw-semibold"><i class="fa fa-filter me-1"></i>Hiển thị:</label>
                            <select id="perPageSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                                <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill">Lọc</button>
                        <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary btn-sm rounded-pill">Xóa lọc</a>
                    </form>
                    <p class="mb-0 text-muted fw-medium">Tổng: {{ $posts->count() }} bài viết</p>
                </div>

                <div class="table-responsive">
                    <table id="postsTable" class="table table-bordered table-hover align-middle text-center" style="font-family: 'Figtree', Arial, Helvetica, sans-serif; border-radius: 1rem; overflow: hidden;">
                        <thead class="table-light">
                            <tr>
                        <th>Tiêu đề</th>
                        <th>Ngày tạo</th>
                        <th>Mô tả ngắn</th>
                        <th>Danh mục</th>
                        <th>Tác giả</th>
                        <th style="display:none;">Nội dung</th>
                        <th style="display:none;">Ngày tạo đầy đủ</th>
                        <th style="display:none;">Người tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($posts as $post)
                    <tr>
                        <td class="fw-semibold">{{ $post->title }}</td>
                        <td>{{ $post->created_at->format('d/m/Y') }}</td>
                        <td class="text-start">{!! nl2br(e($post->short_description ?? '')) !!}</td>
                        <td>{{ $post->category ? $post->category->title : 'Chưa có danh mục' }}</td>
                        <td>{{ $post->author->name ?? 'N/A' }}</td>
                        <td style="display:none;">{{ preg_replace('/\s+/', ' ', strip_tags($post->content ?? '')) }}</td>
                        <td style="display:none;">{{ $post->created_at }}</td>
                        <td style="display:none;">{{ $post->author->name ?? 'N/A' }}</td>
                        <td>
                            <div class="d-flex justify-content-center gap-1 flex-wrap">
                                <a href="{{ route('admin.posts.show', $post) }}" class="btn btn-outline-info btn-sm rounded-pill" title="Xem bài viết"><i class="fa fa-eye"></i></a>
                                @if(Auth::user()->role === 'admin')
                                <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-outline-warning btn-sm rounded-pill" title="Sửa bài viết"><i class="fa fa-edit"></i></a>
                                <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="d-inline sweetalert-delete">
                                    @csrf
    @method('DELETE')
    <button class="btn btn-outline-danger btn-sm rounded-pill" title="Xóa bài viết"><i class="fa fa-trash"></i></button>
</form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<!-- Styles -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" />

<style>
    #postsTable thead th {
        font-weight: 600;
        background-color: #f8f9fa;
    }

    #postsTable tbody tr:hover {
        background-color: #eef4ff;
        transition: all 0.2s;
    }

    .dataTables_wrapper .dt-buttons {
        margin-bottom: 15px;
    }

    /* Icon kích thước cố định và căn giữa */
    .btn-sm i {
        width: 16px;
        height: 16px;
        font-size: 16px;
        text-align: center;
        line-height: 16px;
        display: inline-block;
        vertical-align: middle;
    }

    form.d-inline .btn-sm {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 10px;
        margin: 0;
    }

    /* Ensure all btn-outline icon buttons have consistent size and padding */
    .btn-outline-info.btn-sm,
    .btn-outline-warning.btn-sm,
    .btn-outline-danger.btn-sm {
        width: 30px; /* 20% smaller than 38px */
        height: 30px; /* 20% smaller than 38px */
        padding: 0;
        margin: 0 4px;
        font-size: 14px; /* 20% smaller than 18px */
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        border-width: 2px;
        transition: background-color 0.3s, color 0.3s;
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        .btn-outline-info.btn-sm,
        .btn-outline-warning.btn-sm,
        .btn-outline-danger.btn-sm {
            width: 26px;
            height: 26px;
            font-size: 12px;
            margin: 0 2px;
        }

        .btn-sm i {
            width: 12px;
            height: 12px;
            font-size: 12px;
            line-height: 12px;
        }

        .d-flex.justify-content-center.gap-1.flex-wrap {
            gap: 4px !important;
            justify-content: flex-start !important;
        }

        /* Additional responsive fixes */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        #postsTable {
            width: 100% !important;
            min-width: 600px;
        }

        .card-body {
            padding: 1rem !important;
        }
    }

    .btn-outline-info.btn-sm {
        color: #0d6efd;
        border-color: #0d6efd;
    }

    .btn-outline-info.btn-sm:hover,
    .btn-outline-info.btn-sm:focus {
        background-color: #0d6efd;
        color: white;
    }

    .btn-outline-warning.btn-sm {
        color: #ffc107;
        border-color: #ffc107;
    }

    .btn-outline-warning.btn-sm:hover,
    .btn-outline-warning.btn-sm:focus {
        background-color: #ffc107;
        color: white;
    }

    .btn-outline-danger.btn-sm {
        color: #dc3545;
        border-color: #dc3545;
    }

    .btn-outline-danger.btn-sm:hover,
    .btn-outline-danger.btn-sm:focus {
        background-color: #dc3545;
        color: white;
    }

    .btn-outline-info.btn-sm i,
    .btn-outline-warning.btn-sm i,
    .btn-outline-danger.btn-sm i {
        width: 16px; /* 20% smaller than 20px */
        height: 16px; /* 20% smaller than 20px */
        font-size: 16px; /* 20% smaller than 20px */
        line-height: 16px;
        display: inline-block;
        vertical-align: middle;
        text-align: center;
    }
</style>

<script>
    $(document).ready(function () {
        var perPage = {{ $perPage }};
        var table = $('#postsTable').DataTable({
            dom: 'Bfrtip',
            pageLength: perPage,
            lengthChange: false,
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel"></i> Xuất Excel',
                    className: 'btn btn-success btn-sm rounded-pill shadow-sm',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7] // Include all columns except the last (actions)
                    }
                }
            ],
            language: {
                search: "Tìm kiếm:",
                lengthMenu: "Hiển thị _MENU_ dòng",
                info: "",
                paginate: {
                    previous: "Trước",
                    next: "Sau"
                },
                zeroRecords: "Không tìm thấy kết quả phù hợp",
                infoEmpty: "",
                infoFiltered: ""
            }
        });

        $('#perPageSelect').on('change', function () {
            var selected = parseInt($(this).val());
            table.page.len(selected).draw();
        });

        // Khi thay đổi danh mục cha, cập nhật danh mục con tương ứng
        $('#parentCategorySelect').on('change', function () {
            var parentId = $(this).val();
            var childSelect = $('#childCategorySelect');
            childSelect.empty();
            childSelect.append('<option value="">-- Tất cả --</option>');

            if (parentId) {
                var categories = @json($categories);
                var parentCategory = categories.find(function(cat) {
                    return cat.id == parentId;
                });
                if (parentCategory && parentCategory.children) {
                    parentCategory.children.forEach(function(child) {
                        childSelect.append('<option value="' + child.id + '">' + child.title + '</option>');
                    });
                }
            }
        });

        setTimeout(function () {
            $('.alert-success').fadeOut('slow');
        }, 2500);
    });
</script>
@endsection
