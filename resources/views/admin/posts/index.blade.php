@extends('layouts.app')

@section('content')
<div class="py-4 font-sans">
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                <h4 class="mb-0">Danh sách bài viết</h4>
                <div>
                    <!-- Nút tạo bài viết mới -->
                    <a href="{{ route('posts.create') }}" class="btn btn-success btn-sm me-2">
                        <i class="fa fa-plus me-1"></i> Thêm bài viết
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table id="postsTable" class="table table-bordered table-hover" style="font-family: 'Figtree', Arial, Helvetica, sans-serif;">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Tiêu đề</th>
                                <th>Ngày tạo</th>
                                <th>Mô tả ngắn</th>
                                <th>Tác giả</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($posts as $post)
                            <tr>
                                <td>{{ $post->id }}</td>
                                <td>{{ $post->title }}</td>
                                <td>{{ $post->created_at->format('d/m/Y') }}</td>
                                <td>{!! nl2br(e($post->short_description ?? '')) !!}</td>
                                <td>{{ $post->author->name ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('posts.show', $post) }}" class="btn btn-info btn-sm" title="Xem bài viết"><i class="fa fa-eye"></i></a>
                                    <a href="{{ route('posts.edit', $post) }}" class="btn btn-warning btn-sm" title="Sửa bài viết"><i class="fa fa-edit"></i></a>
                                    <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Xóa</button>
                                    </form>
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

<style>
    /* Đảm bảo font cho tất cả các hàng và ô trong bảng */
    #postsTable tbody tr, 
    #postsTable tbody tr td {
        font-family: 'Figtree', Arial, Helvetica, sans-serif !important;
    }
</style>

<!-- DataTables CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" />

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<!-- Font Awesome (nếu cần) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<script>
    $(document).ready(function () {
        $('#postsTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel"></i> Xuất Excel',
                    className: 'btn btn-success btn-sm',
                    exportOptions: {
                        columns: ':not(:last-child)' // exclude last column (actions)
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
    });
</script>
@endsection
