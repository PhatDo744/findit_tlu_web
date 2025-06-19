@extends('layouts.admin')

@section('title', 'Tổng quan')

@section('content')
<style>
    .rounded-lg {
        border-radius: 10px !important;
    }

    .shadow-lg {
        box-shadow-lg: 0 0.15rem 0.5rem rgba(0, 0, 0, 0.05) !important;
    }

    .bg-purple {
        background-color: #a020f0 !important;
        /* Purple color */
    }

    .text-purple {
        color: #a020f0 !important;
    }
</style>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 page-title">Tổng quan</h1>
        <!-- Có thể thêm nút hành động ở đây nếu cần -->
    </div>

    <!-- Hàng cho các thẻ thống kê -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-lg h-100 py-2 rounded-lg">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary text-white p-3 me-3" style="width: 60px; height: 60px; display: flex; justify-content: center; align-items: center; font-size: 1.5rem;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Tổng người dùng
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_users'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-lg h-100 py-2 rounded-lg">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success text-white p-3 me-3" style="width: 60px; height: 60px; display: flex; justify-content: center; align-items: center; font-size: 1.5rem;">
                        <i class="bi bi-file-earmark-text-fill"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Tổng bài đăng
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_items'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-lg h-100 py-2 rounded-lg">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-warning text-white p-3 me-3" style="width: 60px; height: 60px; display: flex; justify-content: center; align-items: center; font-size: 1.5rem;">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Chờ duyệt
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_items'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-lg h-100 py-2 rounded-lg">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-purple text-white p-3 me-3" style="width: 60px; height: 60px; display: flex; justify-content: center; align-items: center; font-size: 1.5rem;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-purple text-uppercase mb-1">
                            Đã duyệt
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['approved_items'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hàng cho danh sách bài đăng gần đây và người dùng mới -->
    <div class="row">
        <!-- Danh sách bài đăng gần đây -->
        <div class="col-lg-7 mb-4">
            <div class="card shadow-lg mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bài đăng gần đây</h6>
                </div>
                <div class="card-body">
                    @if(isset($recent_items) && $recent_items->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tiêu đề</th>
                                    <th>Người đăng</th>
                                    <th>Ngày đăng</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_items as $item)
                                <tr>
                                    <td>{{ Str::limit($item->title, 40) }}</td>
                                    <td>{{ $item->user->full_name ?? 'N/A' }}</td>
                                    <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        @if($item->status == 'approved')
                                        <span class="badge bg-success-subtle text-success fw-semibold">Đã duyệt</span>
                                        @elseif($item->status == 'pending_approval')
                                        <span class="badge bg-warning-subtle text-warning fw-semibold">Chờ duyệt</span>
                                        @elseif($item->status == 'rejected')
                                        <span class="badge bg-danger-subtle text-danger fw-semibold">Từ chối</span>
                                        @elseif($item->status == 'returned')
                                        <span class="badge bg-primary-subtle text-primary fw-semibold">Trả lại</span>
                                        @elseif($item->status == 'expired')
                                        <span class="badge bg-secondary-subtle text-secondary fw-semibold">Hết hạn</span>
                                        @endif

                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="#" class="btn btn-sm btn-outline-primary">Xem tất cả bài đăng &rarr;</a>
                    </div>
                    @else
                    <p class="text-center text-muted">Chưa có bài đăng nào.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Người dùng mới -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow-lg mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Người dùng mới</h6>
                </div>
                <div class="card-body">
                    @if(isset($new_users) && $new_users->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($new_users as $user)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <img src="{{ $user->photo_url ?? '/images/default_avatar.png' }}" alt="avatar" class="rounded-circle me-2" width="40" height="40">
                                <span>{{ $user->full_name }}</span>
                                <small class="d-block text-muted">{{ $user->email }}</small>
                            </div>
                            @if($user->role_id == 1)
                            <span class="badge bg-purple-subtle text-purple fw-semibold">Quản trị viên</span>
                            @elseif($user->role_id == 2)
                            <span class="badge bg-warning-subtle text-warning fw-semibold">Kiểm duyệt viên</span>
                            @elseif($user->role_id == 3)
                            <span class="badge bg-success-subtle text-success fw-semibold">Người dùng</span>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                    <div class="text-center mt-3">
                        <a href="#" class="btn btn-sm btn-outline-primary">Xem tất cả người dùng &rarr;</a> <!-- Cập nhật route sau -->
                    </div>
                    @else
                    <p class="text-center text-muted">Chưa có người dùng mới nào.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .border-left-primary {
        border-left: .25rem solid #4e73df !important;
    }

    .border-left-success {
        border-left: .25rem solid #1cc88a !important;
    }

    .border-left-info {
        border-left: .25rem solid #36b9cc !important;
    }

    .border-left-warning {
        border-left: .25rem solid #f6c23e !important;
    }

    .text-gray-300 {
        color: #dddfeb !important;
    }

    .card .card-header {
        background-color: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
    }

    .card .card-header h6 {
        color: #1c3d72 !important;
    }
</style>
@endsection

@push('scripts')
// Có thể thêm script riêng cho trang dashboard ở đây nếu cần
@endpush