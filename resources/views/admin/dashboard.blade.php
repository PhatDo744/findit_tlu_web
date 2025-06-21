@extends('layouts.admin')

@section('title', 'Tổng quan')

@push('styles')
<script>
document.body.classList.add('dashboard-page');
</script>
@endpush

@section('content')
<style>
    .dashboard-container {
        height: calc(100vh - 150px);
        display: flex;
        flex-direction: column;
        overflow-y: auto;
        padding-bottom: 30px;
    }

    .stat-cards-section {
        flex-shrink: 0;
        margin-bottom: 1rem;
    }

    .content-section {
        flex: 1;
        min-height: 0;
        display: flex;
        flex-direction: column;
        padding-bottom: 20px;
    }

    .stat-card {
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: #fff;
        height: 80px;
        min-height: 80px;
    }

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: #fff;
        flex-shrink: 0;
    }

    .stat-content {
        flex: 1;
        min-width: 0;
    }

    .stat-label {
        font-size: 0.8rem;
        color: #6b7280;
        font-weight: 500;
        margin-bottom: 2px;
        line-height: 1.2;
    }

    .stat-value {
        font-size: 1.4rem;
        font-weight: 700;
        color: #22223b;
        line-height: 1;
    }

    .stat-blue { background: #2563eb; }
    .stat-green { background: #22c55e; }
    .stat-yellow { background: #f59e42; }
    .stat-purple { background: #a020f0; }

    .card-table {
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        background: #fff;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .card-header-custom {
        padding: 0.875rem 1rem 0.5rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        flex-shrink: 0;
    }

    .card-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        padding: 0.5rem 1rem 0.875rem 1rem;
    }

    .table-container {
        flex: 1;
        overflow-y: auto;
        min-height: 0;
        border-radius: 0 0 12px 12px;
    }

    .table {
        margin-bottom: 0;
        font-size: 0.8rem;
    }

    .table th {
        font-weight: 600;
        color: #374151;
        background: #f8fafc;
        border: none;
        padding: 0.5rem 0.75rem;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .table td {
        vertical-align: middle;
        border: none;
        color: #374151;
        padding: 0.5rem 0.75rem;
        font-size: 0.8rem;
    }

    .badge-status {
        border-radius: 6px;
        font-size: 0.7rem;
        padding: 2px 6px;
        font-weight: 600;
        display: inline-block;
    }

    .badge-approved { background: #dcfce7; color: #166534; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-rejected { background: #fecaca; color: #991b1b; }
    .badge-returned { background: #dbeafe; color: #1e40af; }
    .badge-expired { background: #f3f4f6; color: #6b7280; }
    .badge-admin { background: #f3e8ff; color: #7c3aed; }
    .badge-user { background: #dcfce7; color: #166534; }

    .user-list-container {
        flex: 1;
        overflow-y: auto;
        min-height: 0;
        border-radius: 0 0 12px 12px;
    }

    .list-group-user {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .list-group-user li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem;
        border-bottom: none;
        background: #f8fafc;
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }

    .list-group-user li:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .user-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 0.5rem;
        border: 1px solid #e5e7eb;
        flex-shrink: 0;
    }

    .user-info {
        flex: 1;
        min-width: 0;
    }

    .user-name {
        font-weight: 600;
        color: #374151;
        font-size: 0.8rem;
        line-height: 1.2;
        margin-bottom: 1px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-email {
        font-size: 0.7rem;
        color: #6b7280;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .see-all-link {
        color: #2563eb;
        font-weight: 500;
        text-decoration: none;
        font-size: 0.8rem;
        padding: 0.5rem 0;
        display: block;
        text-align: center;
        flex-shrink: 0;
        border-top: 1px solid #f1f5f9;
    }

    .see-all-link:hover {
        color: #1d4ed8;
        text-decoration: underline;
    }

    /* Responsive adjustments */
    @media (max-width: 1399px) {
        .stat-card {
            padding: 0.875rem;
            height: 75px;
            min-height: 75px;
        }
        
        .stat-icon {
            width: 40px;
            height: 40px;
            font-size: 1.2rem;
        }
        
        .stat-value {
            font-size: 1.25rem;
        }
        
        .stat-label {
            font-size: 0.75rem;
        }
    }

    @media (max-width: 1199px) {
        .dashboard-container {
            height: calc(100vh - 140px);
        }
        
        .stat-card {
            padding: 0.75rem;
            height: 70px;
            min-height: 70px;
            gap: 0.625rem;
        }
        
        .stat-icon {
            width: 36px;
            height: 36px;
            font-size: 1.1rem;
        }
        
        .stat-value {
            font-size: 1.125rem;
        }
        
        .table th, .table td {
            padding: 0.375rem 0.5rem;
        }
    }

    @media (max-width: 991px) {
        .content-section .row {
            flex-direction: column;
        }
        
        .card-table {
            margin-bottom: 1rem;
        }
        
        .dashboard-container {
            height: calc(100vh - 130px);
            overflow-y: auto;
        }
    }

    @media (max-width: 767px) {
        .stat-cards-section .row {
            margin: 0 -0.25rem;
        }
        
        .stat-cards-section .col-6 {
            padding: 0 0.25rem;
            margin-bottom: 0.5rem;
        }
        
        .stat-card {
            padding: 0.625rem;
            height: 65px;
            min-height: 65px;
        }
        
        .stat-icon {
            width: 32px;
            height: 32px;
            font-size: 1rem;
        }
        
        .stat-value {
            font-size: 1rem;
        }
        
        .stat-label {
            font-size: 0.7rem;
        }
    }
</style>

<div class="dashboard-container">
    <!-- Stat Cards Section -->
    <div class="stat-cards-section">
        <div class="row g-2 g-md-3">
            <div class="col-xl-3 col-lg-3 col-md-6 col-6">
                <div class="stat-card">
                    <div class="stat-icon stat-blue">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Tổng người dùng</div>
                        <div class="stat-value">{{ $stats['total_users'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 col-6">
                <div class="stat-card">
                    <div class="stat-icon stat-green">
                        <i class="bi bi-file-earmark-text-fill"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Tổng bài đăng</div>
                        <div class="stat-value">{{ $stats['total_items'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 col-6">
                <div class="stat-card">
                    <div class="stat-icon stat-yellow">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Chờ duyệt</div>
                        <div class="stat-value">{{ $stats['pending_items'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 col-6">
                <div class="stat-card">
                    <div class="stat-icon stat-purple">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Đã duyệt</div>
                        <div class="stat-value">{{ $stats['approved_items'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="content-section">
        <div class="row g-2 g-md-3 h-100">
            <div class="col-lg-8 ">
                <div class="card-table">
                    <div class="card-header-custom">
                        <h6 class="fw-semibold mb-0">Bài đăng gần đây</h6>
                </div>
                    <div class="card-content">
                        <div class="table-container">
                            <table class="table">
                            <thead>
                                <tr>
                                        <th style="width: 35%">Tiêu đề</th>
                                        <th style="width: 25%">Người đăng</th>
                                        <th style="width: 20%">Ngày đăng</th>
                                        <th style="width: 20%">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($recent_items) && $recent_items->count() > 0)
                                    @foreach($recent_items->take(8) as $item)
                                    <tr>
                                        <td>
                                            <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                {{ Str::limit($item->title, 25) }}
                                            </div>
                                        </td>
                                        <td>
                                            <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                {{ Str::limit($item->user->full_name ?? 'N/A', 12) }}
                                            </div>
                                        </td>
                                        <td>{{ $item->created_at->diffForHumans() }}</td>
                                    <td>
                                        @if($item->status == 'approved')
                                                <span class="badge-status badge-approved">Đã duyệt</span>
                                        @elseif($item->status == 'pending_approval')
                                                <span class="badge-status badge-pending">Chờ duyệt</span>
                                        @elseif($item->status == 'rejected')
                                                <span class="badge-status badge-rejected">Từ chối</span>
                                        @elseif($item->status == 'returned')
                                                <span class="badge-status badge-returned">Đã trả</span>
                                        @elseif($item->status == 'expired')
                                                <span class="badge-status badge-expired">Hết hạn</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Chưa có bài đăng nào.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                        <a href="#" class="see-all-link">
                            Xem tất cả bài đăng <i class="bi bi-arrow-right"></i>
                        </a>
                </div>
            </div>
        </div>

            <div class="col-lg-4">
                <div class="card-table">
                    <div class="card-header-custom">
                        <h6 class="fw-semibold mb-0">Người dùng mới</h6>
                </div>
                    <div class="card-content">
                        <div class="user-list-container">
                            <ul class="list-group-user">
                    @if(isset($new_users) && $new_users->count() > 0)
                                    @foreach($new_users->take(8) as $user)
                                    <li>
                                        <div class="d-flex align-items-center flex-grow-1 min-w-0">
                                            <img src="{{ $user->photo_url ?? '/images/default_avatar.png' }}" class="user-avatar" alt="avatar">
                                            <div class="user-info">
                                                <div class="user-name">{{ $user->full_name }}</div>
                                                <div class="user-email">{{ $user->email }}</div>
                                            </div>
                            </div>
                                        <div class="flex-shrink-0">
                            @if($user->role_id == 1)
                                                <span class="badge-status badge-admin">Quản trị viên</span>
                                            @else
                                                <span class="badge-status badge-user">Người dùng</span>
                            @endif
                                        </div>
                        </li>
                        @endforeach
                                @else
                                    <li class="text-center text-muted py-3">Chưa có người dùng mới nào.</li>
                                @endif
                    </ul>
                        </div>
                        <a href="#" class="see-all-link">
                            Xem tất cả người dùng <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection