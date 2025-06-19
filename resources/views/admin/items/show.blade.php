@extends('layouts.admin')

@section('title', 'Chi Tiết Bài Đăng: ' . Str::limit($item->title, 50))

@push('styles')
<style>
    .item-detail-header {
        background-color: #1c3d72;
        /* TLU Blue */
        color: white;
        padding: 1.5rem;
        border-radius: 0.5rem 0.5rem 0 0;
    }

    .item-detail-header h4 {
        margin-bottom: 0.25rem;
    }

    .item-detail-header .badge {
        font-size: 0.9em;
        margin-right: 0.5rem;
    }

    .item-detail-header .item-meta {
        font-size: 0.85em;
        opacity: 0.8;
    }

    .image-gallery img.single-image {
        max-width: 100%;
        height: auto;
        border-radius: 0.25rem;
        margin-bottom: 1rem;
        border: 1px solid #ddd;
        background-color: #f8f9fa;
        /* Light background for image area */
        min-height: 200px;
        /* Minimum height if no image */
        display: block;
        /* Changed to block */
        margin-left: auto;
        margin-right: auto;
    }

    .image-gallery .placeholder-image {
        max-width: 100%;
        height: auto;
        border-radius: 0.25rem;
        margin-bottom: 1rem;
        border: 1px solid #ddd;
        background-color: #f8f9fa;
        min-height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #6c757d;
        font-style: italic;
    }

    .image-gallery .carousel-item img {
        max-height: 500px;
        /* Limit carousel image height */
        object-fit: contain;
        /* Ensure image fits well */
        margin-left: auto;
        margin-right: auto;
    }

    .info-sidebar .card-body dt {
        font-weight: 600;
        color: #333;
    }

    .info-sidebar .card-body dd {
        color: #555;
    }

    .user-avatar-detail {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 10px;
        object-fit: cover;
    }

    .history-log p {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
        padding-left: 1.5rem;
        position: relative;
        border-left: 2px solid #eee;
    }

    .history-log p::before {
        content: '\F282';
        /* Bootstrap Icon for circle-fill */
        font-family: bootstrap-icons !important;
        position: absolute;
        left: -0.5em;
        top: 0.1em;
        color: #1c3d72;
        font-size: 0.8em;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 page-title">Chi tiết bài đăng </h1>
        <!-- Có thể thêm nút hành động ở đây nếu cần -->
    </div>
    <a href="{{ route('admin.items.index') }}" class="mb-2 text-primary d-inline-flex align-items-center gap-1 text-decoration-none fw-medium">
        <i class="bi bi-arrow-left"></i>
        Quay lại danh sách
    </a>

    @include('partials.admin.flash_messages')

    <div class="bg-primary rounded-2 p-3 text-light item-detail-header mb-4 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
        <div>
            <h4>{{ $item->title }}</h4>
            <div>
                <span class="badge bg-{{ $item->item_type == 'lost' ? 'danger-subtle' : 'success-subtle' }} {{ $item->item_type == 'lost' ? 'text-danger' : 'text-success' }}">{{ $item->item_type == 'lost' ? 'Mất đồ' : 'Nhặt được' }}</span>
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

            </div>
        </div>
        <div class="item-meta text-md-end mt-2 mt-md-0">
            Ngày đăng: {{ $item->created_at->format('d/m/Y H:i') }}<br>
            ID: A{{ $item->id }}
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hình ảnh</h6>
                </div>
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
                    <div class="placeholder-image">Chưa có hình ảnh</div>
                    @endif
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Mô tả chi tiết</h6>
                </div>
                <div class="card-body">
                    <p>{!! nl2br(e($item->description)) !!}</p>
                </div>
            </div>

            @if($item->admin_comment && $item->status == 'rejected')
            <div class="card shadow mb-4 border-start border-4 border-danger">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">Lý do từ chối</h6>
                </div>
                <div class="card-body">
                    <p>{{ $item->admin_comment }}</p>
                </div>
            </div>
            @endif

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Lịch sử bài đăng</h6>
                </div>
                <div class="card-body history-log">
                    <p>Bài đăng được tạo bởi {{ $item->user->full_name ?? 'Không rõ' }} vào {{ $item->created_at->format('d/m/Y H:i:s') }}.</p>
                    @if($item->updated_at != $item->created_at && ($item->status == 'approved' || $item->status == 'rejected'))
                    <p>Bài đăng được {{ $item->status == 'approved' ? 'duyệt' : 'từ chối' }} vào {{ $item->updated_at->format('d/m/Y H:i:s') }}.</p>
                    @endif
                    {{-- TODO: Activity Log --}}
                </div>
            </div>

        </div>

        <div class="col-lg-4 info-sidebar">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin bài đăng</h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-8">Danh mục:</dt>
                        <dd class="col-sm-4 text-end">{{ $item->category->name ?? 'N/A' }}</dd>

                        <dt class="col-sm-8">Địa điểm:</dt>
                        <dd class="col-sm-4 text-end">{{ $item->location_description ?? 'Không rõ' }}</dd>

                        <dt class="col-sm-8">Loại:</dt>
                        <dd class="col-sm-4 text-end {{ $item->item_type == 'lost' ? 'text-danger' : 'text-success' }}">{{ $item->item_type == 'lost' ? 'Mất đồ' : 'Nhặt được' }}</dd>

                        <dt class="col-sm-8">Trạng thái:</dt>
                        <dd class="col-sm-4 text-end">
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

                        </dd>

                        <dt class="col-sm-8">Ngày {{ $item->item_type == 'lost' ? 'mất' : 'nhặt' }}:</dt>
                        <dd class="col-sm-4 text-end">{{ $item->date_lost_or_found ? Carbon\Carbon::parse($item->date_lost_or_found)->format('d/m/Y') : 'Không rõ'}}</dd>

                        <dt class="col-sm-8">Liên hệ public:</dt>
                        <dd class="col-sm-4 text-end">{{ $item->is_contact_info_public ? 'Có' : 'Không' }}</dd>

                        @if($item->expiration_date)
                        <dt class="col-sm-8">Ngày hết hạn:</dt>
                        <dd class="col-sm-4 text-end">{{ Carbon\Carbon::parse($item->expiration_date)->format('d/m/Y H:i') }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin người đăng</h6>
                </div>
                <div class="card-body">
                    @if($item->user)
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ $item->user->photo_url ?? '/images/default_avatar.png' }}" alt="{{ $item->user->full_name }}" class="user-avatar-detail">
                        <div>
                            <strong>{{ $item->user->full_name }}</strong><br>
                            <small class="text-muted">{{ $item->user->email }}</small>
                        </div>
                    </div>
                    <dl class="row mb-0">
                        <dt class="col-sm-8">Vai trò:</dt>
                        <dd class="col-sm-4 text-end">
                            @if($item->user->role_id == 1)
                            <span class="badge bg-purple-subtle text-purple fw-semibold">Quản trị viên</span>
                            @elseif($item->user->role_id == 2)
                            <span class="badge bg-warning-subtle text-warning fw-semibold">Kiểm duyệt viên</span>
                            @elseif($item->user->role_id == 3)
                            <span class="badge bg-success-subtle text-success fw-semibold">Người dùng</span>
                            @endif
                        </dd>

                        <dt class="col-sm-8">Trạng thái TK:</dt>
                        <dd class="col-sm-4 text-end">
                            @if($item->user->is_active)
                            <span class="badge  text-success">Hoạt động</span>
                            @else
                            <span class="badge  text-danger">Bị khóa</span>
                            @endif
                        </dd>

                        <dt class="col-sm-8">Tham gia:</dt>
                        <dd class="col-sm-4 text-end">{{ $item->user->created_at->format('d/m/Y') }}</dd>
                    </dl>
                    @else
                    <p class="text-muted">Không có thông tin người đăng.</p>
                    @endif
                </div>
            </div>

            @if($item->user)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thống kê người dùng</h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-8">Tổng bài đăng:</dt>
                        <dd class="col-sm-4 text-end">{{ $item->user->items_count ?? 0 }}</dd>

                        <dt class="col-sm-8">Đã duyệt:</dt>
                        <dd class="col-sm-4 text-end text-success">{{ $item->user->approved_items_count ?? 0 }}</dd>

                        <dt class="col-sm-8">Chờ duyệt:</dt>
                        <dd class="col-sm-4 text-end text-danger">{{ $item->user->pending_items_count ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
            @endif

            @if($item->status == 'pending_approval')
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hành động</h6>
                </div>
                <div class="card-body text-center">
                    <button type="button" class="btn btn-success me-2"
                        data-bs-toggle="modal" data-bs-target="#approveItemModalShowPage"
                        data-item-id="{{ $item->id }}" data-item-title="{{ $item->title }}">
                        <i class="bi bi-check-circle-fill me-1"></i> Duyệt bài
                    </button>
                    <button type="button" class="btn btn-danger"
                        data-bs-toggle="modal" data-bs-target="#rejectItemModalShowPage"
                        data-item-id="{{ $item->id }}" data-item-title="{{ $item->title }}">
                        <i class="bi bi-x-circle-fill me-1"></i> Từ chối
                    </button>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<!-- Modal Xác nhận Duyệt (cho trang show) -->
<div class="modal fade" id="approveItemModalShowPage" tabindex="-1" aria-labelledby="approveItemModalShowPageLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="approveItemFormShowPage" method="POST" action="">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="approveItemModalShowPageLabel">Xác nhận duyệt bài đăng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn duyệt bài đăng "<strong id="approveItemTitleShowPage"></strong>" không?
                    <input type="hidden" name="status" value="approved">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">Duyệt bài</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Xác nhận Từ chối (cho trang show) -->
<div class="modal fade" id="rejectItemModalShowPage" tabindex="-1" aria-labelledby="rejectItemModalShowPageLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="rejectItemFormShowPage" method="POST" action="">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectItemModalShowPageLabel">Xác nhận từ chối bài đăng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn từ chối bài đăng "<strong id="rejectItemTitleShowPage"></strong>" không?</p>
                    <input type="hidden" name="status" value="rejected">
                    <div class="mb-3">
                        <label for="admin_comment_show_page" class="form-label">Lý do từ chối (nếu có):</label>
                        <textarea class="form-control" id="admin_comment_show_page" name="admin_comment" rows="3"></textarea>
                    </div>
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2"></i>
                        <div>
                            Lưu ý: Hành động này không thể hoàn tác. Bài đăng sẽ bị từ chối và người dùng sẽ nhận được thông báo.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Từ chối bài</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function setupModal(modalId, titleElementId, formId, actionUrlGenerator) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const itemId = button.getAttribute('data-item-id');
                    const itemTitle = button.getAttribute('data-item-title');

                    if (titleElementId) {
                        modal.querySelector(titleElementId).textContent = itemTitle;
                    }

                    const form = modal.querySelector(formId);
                    form.action = actionUrlGenerator(itemId);
                });
            }
        }

        setupModal('approveItemModalShowPage', '#approveItemTitleShowPage', '#approveItemFormShowPage', (itemId) => `{{ url('admin/items') }}/${itemId}/update-status`);
        setupModal('rejectItemModalShowPage', '#rejectItemTitleShowPage', '#rejectItemFormShowPage', (itemId) => `{{ url('admin/items') }}/${itemId}/update-status`);

        const rejectItemModalShowPageElement = document.getElementById('rejectItemModalShowPage');
        if (rejectItemModalShowPageElement) {
            rejectItemModalShowPageElement.addEventListener('hidden.bs.modal', function() {
                const textarea = rejectItemModalShowPageElement.querySelector('#admin_comment_show_page');
                if (textarea) {
                    textarea.value = '';
                }
            });
        }
    });
</script>
@endpush