<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $userCount = User::count();
        $postCount = Post::count();

        // Lấy bài viết mới nhất
        $latestPost = Post::orderBy('created_at', 'desc')->first();

        // Lấy ngày tạo bài viết mới nhất
        $latestPostDate = $latestPost ? $latestPost->created_at : null;

        // Lấy dữ liệu tăng trưởng người dùng theo tháng 12 tháng gần nhất
        $userGrowth = User::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', Carbon::now()->subMonths(12))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        // Lấy dữ liệu tăng trưởng bài viết theo tháng 12 tháng gần nhất
        $postGrowth = Post::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', Carbon::now()->subMonths(12))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        // Lấy danh sách bài viết để hiển thị trên dashboard
        $posts = Post::with('author')->get();

        return view('admin.dashboard', compact(
            'userCount', 'postCount',
            'latestPost', 'latestPostDate',
            'userGrowth', 'postGrowth', 'posts'
        ));
    }

    /**
     * Xuất file Excel danh sách bài viết
     */
    public function exportPosts()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PostExport, 'danh-sach-bai-viet.xlsx');
    }
}
