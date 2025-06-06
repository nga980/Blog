@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="container">
        <!-- Thống kê -->
        <div class="row mb-4 justify-content-center g-3">
            <div class="col-auto">
                <div class="card text-white shadow-sm rounded-4 p-2" style="min-width: 160px; max-height: 90px; background: #4e73df;">
                    <div class="card-body text-center p-2 d-flex flex-column justify-content-center" style="height: 90px;">
                        <h6 class="card-title fs-6 mb-1"><i class="fas fa-users me-2"></i>Tổng người dùng</h6>
                        <p class="card-text fs-4 fw-semibold mb-0">{{ $userCount ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <div class="card text-white shadow-sm rounded-4 p-2" style="min-width: 160px; max-height: 90px; background: #1cc88a;">
                    <div class="card-body text-center p-2 d-flex flex-column justify-content-center" style="height: 90px;">
                        <h6 class="card-title fs-6 mb-1"><i class="fas fa-file-alt me-2"></i>Tổng bài viết</h6>
                        <p class="card-text fs-4 fw-semibold mb-0">{{ $postCount ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quản lý bài viết -->
        <div class="card shadow-sm border-0 rounded-4 mb-4 p-3">
            <div class="card-body d-flex justify-content-center flex-wrap gap-2">
                <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-primary shadow-sm px-4 py-2">
                    <i class="fas fa-list me-2"></i> Quản lý bài viết
                </a>
                <a href="{{ route('admin.posts.create') }}" class="btn btn-outline-success shadow-sm px-4 py-2">
                    <i class="fas fa-plus me-2"></i> Thêm bài viết
                </a>
            </div>
        </div>

        <!-- Biểu đồ tăng trưởng -->
        <div class="card shadow-sm border-0 rounded-4 p-3 mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3 text-center">Tăng trưởng người dùng và bài viết theo tháng</h5>
                <div class="chart-container" style="overflow-x: auto;">
                    <canvas id="growthChart" height="300" style="min-width: 500px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('growthChart').getContext('2d');

        const userGrowth = @json($userGrowth);
        const postGrowth = @json($postGrowth);

        const labels = [];
        const userDataMap = {};
        const postDataMap = {};

        userGrowth.forEach(item => {
            userDataMap[item.month] = item.count;
            if (!labels.includes(item.month)) {
                labels.push(item.month);
            }
        });

        postGrowth.forEach(item => {
            postDataMap[item.month] = item.count;
            if (!labels.includes(item.month)) {
                labels.push(item.month);
            }
        });

        labels.sort();

        const userData = labels.map(label => userDataMap[label] || 0);
        const postData = labels.map(label => postDataMap[label] || 0);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        type: 'line',
                        label: 'Người dùng',
                        data: userData,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        tension: 0.3,
                        fill: false,
                        yAxisID: 'y',
                    },
                    {
                        type: 'bar',
                        label: 'Bài viết',
                        data: postData,
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                stacked: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Người dùng'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        grid: {
                            drawOnChartArea: false,
                        },
                        title: {
                            display: true,
                            text: 'Bài viết'
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
</script>
@endsection
