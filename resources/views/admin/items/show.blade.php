@extends('layouts.admin')

@section('title', 'Chi tiết bài đăng')

@push('styles')
<style>
    body {
        background-color: #f5f5f5;
    }

    .content {
        padding: 4px;
    }

    .post-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        padding: 25px 30px;
        border-radius: 8px;
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .post-title {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .post-title h2 {
        font-size: 22px;
        font-weight: 500;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 500;
    }

    .status-lost {
        background-color: #ff4444;
        color: white;
    }

    .status-approved {
        background-color: #4caf50;
        color: white;
    }

    .status-pending {
        background-color: #ff9800;
        color: white;
    }

    .status-rejected {
        background-color: #e53935;
        color: white;
    }

    .status-returned {
        background-color: #2196f3;
        color: white;
    }

    .post-meta {
        text-align: right;
        font-size: 14px;
        opacity: 0.9;
    }

    .main-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
    }

    .post-content {
        background: white;
        border-radius: 8px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #333;
    }

    .image-placeholder,
    .post-image,
    .single-image,
    .carousel-inner img {
        width: 100%;
        height: 300px;
        background-color: #f0f0f0;
        border: 2px dashed #ccc;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
        font-size: 16px;
        margin-bottom: 30px;
        object-fit: contain;
    }

    @media (max-width: 600px) {

        .image-placeholder,
        .post-image,
        .single-image,
        .carousel-inner img {
            height: 180px !important;
            max-height: 180px !important;
            width: 100% !important;
            object-fit: contain !important;
        }

        .content {
            padding: 10px;
        }

        .post-content {
            padding: 10px;
        }
    }

    .description {
        background-color: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        border-left: 4px solid #2a5298;
        line-height: 1.6;
        color: #555;
    }

    .timeline {
        border-top: 1px solid #eee;
        padding-top: 20px;
        margin-top: 20px;
    }

    .timeline-item {
        font-size: 13px;
        color: #666;
        margin-bottom: 8px;
        line-height: 1.4;
    }

    .sidebar-right {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .info-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .info-card h3 {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 15px;
        color: #333;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 14px;
    }

    .info-label {
        color: #666;
        font-weight: 500;
    }

    .info-value {
        font-weight: 500;
        color: #333;
    }

    .info-value.lost {
        color: #ff4444;
    }

    .info-value.approved {
        color: #4caf50;
    }

    .info-value.pending {
        color: #ff9800;
    }

    .info-value.returned {
        color: #2196f3;
    }

    .user-profile {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 15px;
    }

    .user-profile-avatar {
        width: 40px;
        height: 40px;
        background-color: #2a5298;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 16px;
    }

    .user-profile-info h4 {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .user-profile-info p {
        font-size: 12px;
        color: #666;
    }

    .user-status {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 500;
        margin-left: 8px;
        background: #fff3cd;
        color: #856404;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .stat-item {
        text-align: center;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 6px;
    }

    .stat-number {
        font-size: 20px;
        font-weight: bold;
        color: #2a5298;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 12px;
        color: #666;
    }

    @media (max-width: 991px) {
        .main-grid {
            grid-template-columns: 1fr;
        }

        .sidebar-right {
            flex-direction: row;
            gap: 10px;
        }
    }
</style>
@endpush

@section('content')
<div class="content">
    <a href="{{ route('admin.items.index') }}" class="btn btn-link mb-3 px-0" style="font-weight:500;font-size:16px;text-decoration:none !important;"><i class="bi bi-arrow-left"></i> Quay lại danh sách</a>
    <div class="post-header">
        <div class="post-title">
            <h2>{{ $item->title }}</h2>
            <span class="status-badge status-{{ $item->item_type == 'lost' ? 'lost' : 'found' }}">{{ $item->item_type == 'lost' ? 'Mất đồ' : 'Nhặt được' }}</span>
            @if($item->status == 'approved')
            <span class="status-badge status-approved">Đã duyệt</span>
            @elseif($item->status == 'pending_approval')
            <span class="status-badge status-pending">Chờ duyệt</span>
            @elseif($item->status == 'rejected')
            <span class="status-badge status-rejected">Từ chối</span>
            @elseif($item->status == 'returned')
            <span class="status-badge status-returned">Đã trả/tìm thấy</span>
            @elseif($item->status == 'expired')
            <span class="status-badge status-expired">Hết hạn</span>
            @endif
        </div>
        <div class="post-meta">
            <div>Ngày đăng: {{ $item->created_at->format('d/m/Y H:i') }}</div>
            <div>ID: A{{ $item->id }}</div>
        </div>
    </div>
    <div class="main-grid">
        <div class="post-content">
            <h3 class="section-title">Hình ảnh</h3>
            <div class="card-body image-gallery">
                @if($item->images && $item->images->count() > 0)
                @if($item->images->count() == 1)
                <img src="{{ $item->images->first()->image_url }}" alt="Hình ảnh bài đăng" class="single-image">
                @else
                <div id="itemImageCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        @foreach($item->images as $index => $image)
                        <button type="button" data-bs-target="#itemImageCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}" aria-current="{{ $index == 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                    <div class="carousel-inner">
                        @foreach($item->images as $index => $image)
                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                            <img src="{{ $image->image_url }}" class="d-block w-100" alt="Hình ảnh {{ $index + 1 }}">
                        </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#itemImageCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#itemImageCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
                @endif
                @else
                <div class="image-placeholder">Ảnh minh họa bài đăng</div>
                @endif
            </div>
            <h3 class="section-title">Mô tả chi tiết</h3>
            <div class="description">{!! nl2br(e($item->description)) !!}</div>
            <div class="timeline">
                <div class="timeline-item">
                    Bài đăng được tạo bởi {{ $item->user->full_name ?? 'Không rõ' }} vào {{ $item->created_at->format('d/m/Y H:i:s') }}.
                </div>
                @if($item->updated_at != $item->created_at && ($item->status == 'approved' || $item->status == 'rejected'))
                <div class="timeline-item">
                    Bài đăng được {{ $item->status == 'approved' ? 'duyệt' : 'từ chối' }} vào {{ $item->updated_at->format('d/m/Y H:i:s') }}.
                </div>
                @endif
            </div>
            @if($item->admin_comment && $item->status == 'rejected')
            <div class="timeline-item" style="color:#e53935; font-weight:600;">Lý do từ chối: {{ $item->admin_comment }}</div>
            @endif
        </div>
        <div class="sidebar-right">
            <div class="info-card">
                <h3>Thông tin bài đăng</h3>
                <div class="info-row">
                    <span class="info-label">Danh mục:</span>
                    <span class="info-value">{{ $item->category->name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Địa điểm:</span>
                    <span class="info-value">{{ $item->location_description ?? 'Không rõ' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Loại:</span>
                    <span class="info-value lost">{{ $item->item_type == 'lost' ? 'Mất đồ' : 'Nhặt được' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Trạng thái:</span>
                    <span class="info-value approved">@if($item->status == 'approved') Đã duyệt @elseif($item->status == 'pending_approval') Chờ duyệt @elseif($item->status == 'rejected') Từ chối @elseif($item->status == 'returned') Đã trả/tìm thấy @elseif($item->status == 'expired') Hết hạn @endif</span>
                </div>
            </div>
            <div class="info-card">
                <h3>Thông tin người đăng</h3>
                <div class="user-profile">
                    @if($item->user->photo_url)
                    <img src="{{ $item->user->photo_url }}" alt="avatar" class="user-avatar" style="width:40px;height:40px;border-radius:50%;object-fit:cover;margin-right:12px;">
                    @else
                    <div class="user-profile-avatar">{{ strtoupper(mb_substr($item->user->full_name ?? 'A', 0, 1)) }}</div>
                    @endif
                    <div class="user-profile-info">
                        <h4>{{ $item->user->full_name ?? 'N/A' }}</h4>
                        <p>{{ $item->user->email ?? '' }}</p>
                    </div>
                    <span class="user-status active">@if($item->user->is_active) Hoạt động @else Bị khóa @endif</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Vai trò:</span>
                    <span class="info-value">@if($item->user->role_id == 1) Quản trị viên @elseif($item->user->role_id == 2) Kiểm duyệt viên @else Người dùng @endif</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tham gia:</span>
                    <span class="info-value">{{ $item->user->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
            <div class="info-card">
                <h3>Thống kê người dùng</h3>
                <div class="info-row">
                    <span class="info-label">Tổng bài đăng:</span>
                    <span class="info-value">{{ $item->user->items_count ?? 0 }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Đã duyệt:</span>
                    <span class="info-value" style="color: #4caf50">{{ $item->user->approved_items_count ?? 0 }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Chờ duyệt:</span>
                    <span class="info-value" style="color: #ff9800">{{ $item->user->pending_items_count ?? 0 }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Đã trả/tìm thấy:</span>
                    <span class="info-value" style="color: #2196f3">{{ $item->user->returned_items_count ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection