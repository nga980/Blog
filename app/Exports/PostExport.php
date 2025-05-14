<?php

namespace App\Exports;

use App\Models\Post;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PostExport implements FromCollection, WithHeadings
{
    /**
     * Lấy dữ liệu bài viết để xuất Excel
     */
    public function collection()
    {
        return Post::select('id', 'title', 'short_description', 'created_at')->get();
    }

    /**
     * Định nghĩa tiêu đề các cột trong file Excel
     */
    public function headings(): array
    {
        return [
            'ID',
            'Tiêu đề',
            'Mô tả ngắn',
            'Ngày tạo',
        ];
    }
}
