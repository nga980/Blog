<?php

namespace App\Exports;

use App\Models\Post;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class PostExport implements FromCollection, WithHeadings
{
    /**
     * Lấy dữ liệu bài viết để xuất Excel
     */
    public function collection()
    {
        $posts = Post::select('id', 'title', 'short_description', 'content', 'banner', 'gallery', 'created_at')->get();

        // Map posts to format banner and gallery fields as strings
        return $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'short_description' => $post->short_description,
                'content' => $post->content,
                'banner' => $post->banner ? asset('storage/' . $post->banner) : '',
                'gallery' => $post->gallery ? implode(', ', json_decode($post->gallery, true)) : '',
                'created_at' => $post->created_at,
            ];
        });
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
            'Nội dung',
            'Banner',
            'Gallery',
            'Ngày tạo',
        ];
    }
}
