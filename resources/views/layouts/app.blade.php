<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- SweetAlert2 CSS (Tùy chọn, nếu bạn muốn tải CSS riêng biệt thay vì dùng bản all.min.js) --}}
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.min.css"> --}}
</head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>
        </div>

        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Toast notification mixin (dùng cho các thông báo ít quan trọng hơn, tự đóng)
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                // --- Thông báo SweetAlert2 chuẩn (từ PostController với 'swal_success'/'swal_error') ---
                @if (session('swal_success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công!',
                        text: '{{ session('swal_success') }}',
                        showConfirmButton: true // Yêu cầu người dùng bấm OK
                    });
                @endif

                @if (session('swal_error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: '{{ session('swal_error') }}',
                        showConfirmButton: true // Yêu cầu người dùng bấm OK
                    });
                @endif

                // --- Thông báo lỗi validation từ Laravel ($errors->any()) ---
                @if ($errors->any())
                    let errorMessages = '';
                    @foreach ($errors->all() as $error)
                        errorMessages += '{{ $error }}<br>';
                    @endforeach
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi nhập liệu!',
                        html: errorMessages, // Hiển thị nhiều lỗi dưới dạng HTML
                        showConfirmButton: true
                    });
                @endif

                // --- Các thông báo Toast hiện có (nếu vẫn được sử dụng ở các phần khác của ứng dụng) ---
                // Giữ lại các đoạn này nếu các controller khác không dùng 'swal_success'/'swal_error'
                // mà vẫn dùng 'success', 'error', 'warning', 'info' thông thường.
                @if(session('success'))
                    Toast.fire({
                        icon: 'success',
                        title: '{{ session('success') }}'
                    });
                @endif

                @if(session('error'))
                    Toast.fire({
                        icon: 'error',
                        title: '{{ session('error') }}'
                    });
                @endif

                @if(session('warning'))
                    Toast.fire({
                        icon: 'warning',
                        title: '{{ session('warning') }}'
                    });
                @endif

                @if(session('info'))
                    Toast.fire({
                        icon: 'info',
                        title: '{{ session('info') }}'
                    });
                @endif

                // Hộp thoại xác nhận khi xóa form
                document.querySelectorAll('form.sweetalert-delete').forEach(function(form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Bạn có chắc muốn xóa mục này?',
                            text: 'Hành động này không thể hoàn tác!',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Xóa',
                            cancelButtonText: 'Hủy'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });

                // Hộp thoại xác nhận cho form sửa mà không có thay đổi
                document.querySelectorAll('form.sweetalert-confirm-nochange').forEach(function(form) {
                    // Cần một cách phức tạp hơn để theo dõi thay đổi của form,
                    // đặc biệt với các input file, checkbox, radio.
                    // Đây là một cách đơn giản, có thể không hoạt động hoàn hảo với mọi loại input.
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

                    form.addEventListener('submit', function(e) {
                        let isChanged = false;
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
                                // Nếu có file mới được chọn, coi như có thay đổi
                                isChanged = true;
                            }
                        });

                        if (!isChanged) {
                            e.preventDefault(); // Ngăn gửi form mặc định
                            Swal.fire({
                                title: 'Bạn chưa thay đổi gì.',
                                text: 'Bạn có muốn gửi form mà không có thay đổi không?',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Gửi',
                                cancelButtonText: 'Hủy'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    form.submit(); // Gửi form nếu người dùng xác nhận
                                }
                            });
                        }
                    });
                });
            });
        </script>
    </body>
</html>
