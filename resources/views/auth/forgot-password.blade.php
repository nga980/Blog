<x-guest-layout>
    {{-- Tiêu đề trang --}}
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">
        {{ __('Đặt lại mật khẩu') }}
    </h2>

    {{-- Mô tả hướng dẫn --}}
    <div class="mb-6 text-sm text-gray-700 leading-relaxed text-center">
        {{ __('Bạn quên mật khẩu? Đừng lo. Chỉ cần cho chúng tôi biết địa chỉ email của bạn và chúng tôi sẽ gửi cho bạn một liên kết đặt lại mật khẩu để bạn có thể chọn mật khẩu mới.') }}
    </div>

    <!-- Session Status (để hiển thị thông báo thành công sau khi gửi email) -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-4"> {{-- Thêm mb-4 để tạo khoảng cách dưới email input --}}
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex flex-col items-center mt-6 space-y-4"> {{-- Thay đổi bố cục nút bấm --}}
            <x-primary-button class="w-full justify-center"> {{-- Làm nút bấm rộng đầy đủ và căn giữa chữ --}}
                {{ __('Gửi liên kết đặt lại mật khẩu') }}
            </x-primary-button>

            {{-- Nút quay lại đăng nhập, đặt dưới nút gửi, thêm hiệu ứng chuyển động --}}
            <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900 underline-offset-4 hover:underline transition duration-150 ease-in-out">
                {{ __('Quay lại đăng nhập') }}
            </a>
        </div>
    </form>
</x-guest-layout>