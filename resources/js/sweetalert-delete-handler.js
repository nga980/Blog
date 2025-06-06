import Swal from 'sweetalert2';

document.addEventListener('DOMContentLoaded', () => {
    const deleteForms = document.querySelectorAll('form.sweetalert-delete');

    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                icon: 'warning',
                title: 'Bạn có chắc muốn xóa mục này?',
                text: 'Hành động này không thể hoàn tác!',
                showCancelButton: true,
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
