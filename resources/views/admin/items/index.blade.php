@extends('layouts.admin')

@section('title', 'Quản lý Bài đăng')

@push('styles')
<style>
    .item-thumbnail {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #eee;
    }
    .table th, .table td {
        vertical-align: middle;
        text-align: center;
        font-size: 0.98rem;
        padding: 14px 8px;
    }
    .table thead th {
        background-color: #1c3d72;
        color: #fff;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 1rem;
        padding: 16px;
        border-bottom: none;
    }
    .badge-status {
        font-size: 0.85rem;
        padding: 0.4em 0.7em;
        min-width: 90px;
        text-align: center;
        border-radius: 8px;
    }
    .action-icons {
        padding: 4px !important;
        white-space: nowrap;
    }
    .action-icons a, .action-icons button {
        margin-right: 5px;
        color: #6c757d;
        background: none;
        border: none;
        padding: 0;
        font-size: 1.2rem;
    }
    .action-icons a:hover, .action-icons button:hover {
        color: #1c3d72;
    }
    .action-icons .btn-delete:hover { color: #dc3545; }
    .action-icons .btn-approve:hover { color: #198754; }
    .action-icons .btn-reject:hover { color: #dc3545; }
    .item-description-short {
        font-size: 0.85em;
        color: #6c757d;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 300px;
        margin: 0 auto;
    }
    .table-responsive {
        overflow-x: auto;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    @include('partials.admin.flash_messages')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh Sách Bài Đăng</h6>
        </div>
        <div class="card-body">
            <!-- Bộ lọc -->
            <form method="GET" action="{{ route('admin.items.index') }}" class="mb-4 row gx-3 gy-2 align-items-center form-filter">
                <div class="col-md-4 col-lg-3">
                    <input type="text" class="form-control" name="search_term" placeholder="Tìm tiêu đề, mô tả..." value="{{ request('search_term') }}">
                </div>
                <div class="col-md-3 col-lg-2">
                    <select class="form-select" name="status">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending_approval" {{ request('status') == 'pending_approval' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Đã trả/tìm thấy</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Hết hạn</option>
                    </select>
                </div>
                <div class="col-md-2 col-lg-2">
                    <select class="form-select" name="item_type">
                        <option value="">Tất cả loại</option>
                        <option value="lost" {{ request('item_type') == 'lost' ? 'selected' : '' }}>Mất đồ</option>
                        <option value="found" {{ request('item_type') == 'found' ? 'selected' : '' }}>Nhặt được</option>
                    </select>
                </div>
                <div class="col-md-3 col-lg-2">
                    <select class="form-select" name="category_id">
                        <option value="">Tất cả danh mục</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-lg-1">
                    <button type="submit" style="background-color: #1c3d72;" class="btn text-light  w-100"><i class="bi bi-search"></i><span class="">Tìm kiếm</span></button>
                </div>
                <div class="col-md-2 col-lg-2">
                    <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary w-100"><i class="bi bi-arrow-clockwise me-1"></i> Reset</a>
                </div>
            </form>
        </div>
    </div>
    <div class="card shadow">
        <div class="table-responsive">
            <table class="table my-custom-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Hình ảnh</th>
                        <th>Tiêu đề</th>
                        <th>Loại</th>
                        <th>Danh mục</th>
                        <th>Người đăng</th>
                        <th>Trạng thái</th>
                        <th>Ngày đăng</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $index => $item)
                    <tr>
                        <td>{{ $items->firstItem() + $index }}</td>
                        <td>
                            <img src="{{ $item->images->first()->image_url ?? '/images/default_item_placeholder.png' }}" alt="{{ Str::limit($item->title, 20) }}" class="item-thumbnail">
                        </td>
                        <td>
                            <div style="font-weight:600;">{{ $item->title }}</div>
                            <div class="item-description-short">{{ $item->description }}</div>
                        </td>
                        <td>
                            @if($item->item_type == 'lost')
                            <span class="badge bg-danger-subtle text-danger badge-status">Mất đồ</span>
                            @else
                            <span class="badge bg-success-subtle text-success badge-status">Nhặt được</span>
                            @endif
                        </td>
                        <td>{{ $item->category->name ?? 'N/A' }}</td>
                        <td>{{ $item->user->full_name ?? 'N/A' }}</td>
                        <td>
                            @if($item->status == 'approved')
                            <span class="badge bg-success-subtle text-success badge-status">Đã duyệt</span>
                            @elseif($item->status == 'pending_approval')
                            <span class="badge bg-warning-subtle text-warning badge-status">Chờ duyệt</span>
                            @elseif($item->status == 'rejected')
                            <span class="badge bg-danger-subtle text-danger badge-status">Từ chối</span>
                            @elseif($item->status == 'returned')
                            <span class="badge bg-primary-subtle text-primary badge-status">Đã trả/tìm thấy</span>
                            @elseif($item->status == 'expired')
                            <span class="badge bg-secondary-subtle text-secondary badge-status">Hết hạn</span>
                            @endif
                        </td>
                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                        <td class="action-icons p-3">
                            <a href="{{ route('admin.items.show', $item->id) }}" title="Xem chi tiết" class="btn btn-action-icon btn-action-primary bg-primary-subtle">
                                <i class="bi bi-eye text-primary p-2"></i>
                            </a>
                            @if($item->status == 'pending_approval')
                            <button type="button" class="btn btn-action-icon btn-action-success bg-success-subtle" title="Duyệt bài" data-bs-toggle="modal" data-bs-target="#approveItemModal" data-item-id="{{ $item->id }}" data-item-title="{{ $item->title }}" data-item-type="{{ $item->item_type }}" data-item-category="{{ $item->category->name ?? '' }}" data-item-user="{{ $item->user->full_name ?? '' }}" data-item-status="{{ $item->status }}">
                                <i class="bi bi-check text-success p-2"></i>
                            </button>
                            <button type="button" class="btn btn-action-icon btn-action-danger bg-danger-subtle" title="Từ chối bài" data-bs-toggle="modal" data-bs-target="#rejectItemModal" data-item-id="{{ $item->id }}" data-item-title="{{ $item->title }}" data-item-type="{{ $item->item_type }}" data-item-category="{{ $item->category->name ?? '' }}" data-item-user="{{ $item->user->full_name ?? '' }}" data-item-status="{{ $item->status }}">
                                <i class="bi bi-x text-danger p-2"></i>
                            </button>
                            @endif
                            <button type="button" class="btn btn-action-icon btn-action-secondary bg-secondary-subtle" title="Xóa bài" data-bs-toggle="modal" data-bs-target="#deleteItemModal" data-item-id="{{ $item->id }}" data-item-title="{{ $item->title }}" data-item-type="{{ $item->item_type }}" data-item-category="{{ $item->category->name ?? '' }}" data-item-user="{{ $item->user->full_name ?? '' }}" data-item-status="{{ $item->status }}">
                                <i class="bi bi-trash text-secondary p-2"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">Không tìm thấy bài đăng nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($items->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <small>Hiển thị {{ $items->firstItem() }} đến {{ $items->lastItem() }} của {{ $items->total() }} kết quả</small>
        {{ $items->appends(request()->query())->links() }}
    </div>
    @endif
</div>
<!-- Modal Xác nhận Duyệt -->
<div class="modal fade" id="approveItemModal" tabindex="-1" aria-labelledby="approveItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="approveItemForm" method="POST" action="">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="approveItemModalLabel">Xác nhận duyệt bài đăng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-0">
                    <p class="mb-3 text-secondary small">Bài đăng sẽ được hiển thị công khai</p>
                    <div class="alert alert-success bg-success-subtle border-success-subtle text-success-emphasis d-flex align-items-start p-3 mb-4 rounded">
                        <i class="fas fa-check-circle fs-5 me-2" style="color: #198754;"></i>
                        <div>
                            <h6 class="alert-heading fw-bold mb-2">Thông tin duyệt bài</h6>
                            <ul class="mb-0 ps-3 small">
                                <li>Bài đăng sẽ hiển thị công khai trên ứng dụng</li>
                                <li>Người dùng sẽ nhận được thông báo</li>
                                <li>Bài đăng có thể được tìm kiếm bởi người khác</li>
                                <li>Trạng thái sẽ chuyển thành "Đã duyệt"</li>
                            </ul>
                        </div>
                    </div>
                    <div class="card border-0 bg-light-subtle mb-4">
                        <div class="card-body p-3">
                            <h6 class="card-title fw-bold mb-3 d-flex align-items-center">
                                <i class="fas fa-file-alt me-2 text-secondary"></i> Thông tin bài đăng
                            </h6>
                            <div class="d-flex mb-2 small text-secondary">
                                <span class="me-auto text-dark">Tiêu đề:</span>
                                <span class="text-end text-dark fw-medium" id="approveItemTitle"></span>
                            </div>
                            <div class="d-flex mb-2 small text-secondary">
                                <span class="me-auto text-dark">Loại bài đăng:</span>
                                <span class="text-end text-dark fw-medium badge" id="approveItemType"></span>
                            </div>
                            <div class="d-flex mb-2 small text-secondary">
                                <span class="me-auto text-dark">Danh mục:</span>
                                <span class="text-end text-dark fw-medium" id="approveItemCategory"></span>
                            </div>
                            <div class="d-flex mb-2 small text-secondary">
                                <span class="me-auto text-dark">Người đăng:</span>
                                <span class="text-end text-dark fw-medium" id="approveItemUser"></span>
                            </div>
                            <div class="d-flex mb-0 align-items-center small text-secondary">
                                <span class="me-auto text-dark">Trạng thái hiện tại:</span>
                                <span id="approveItemStatusBadge"></span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 border rounded p-3 bg-light-subtle">
                        <label for="admin_note" class="form-label fw-bold text-dark mb-2 d-flex align-items-center small">
                            <i class="fas fa-user-tie me-2 text-secondary"></i> Ghi chú kiểm duyệt
                        </label>
                        <textarea class="form-control" id="admin_note" name="admin_note" rows="3" placeholder="Nhập ghi chú cho việc duyệt bài (tùy chọn)..."></textarea>
                    </div>
                    <input type="hidden" name="status" value="approved">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-success">Duyệt bài</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Xác nhận Từ chối -->
<div class="modal fade" id="rejectItemModal" tabindex="-1" aria-labelledby="rejectItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="rejectItemForm" method="POST" action="">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectItemModalLabel">Xác nhận từ chối bài đăng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-0">
                    <p class="mb-3 text-secondary small">Bài đăng sẽ không được hiển thị công khai</p>
                    <div class="alert alert-danger bg-danger-subtle border-danger-subtle text-danger-emphasis d-flex align-items-start p-3 mb-4 rounded">
                        <i class="fas fa-exclamation-triangle fs-5 me-2" style="color: #dc3545;"></i>
                        <div>
                            <h6 class="alert-heading fw-bold mb-2">Lưu ý quan trọng</h6>
                            <ul class="mb-0 ps-3 small">
                                <li>Bài đăng sẽ bị từ chối và không hiển thị công khai</li>
                                <li>Người dùng sẽ nhận được thông báo</li>
                                <li>Hành động này không thể hoàn tác</li>
                                <li>Người dùng có thể chỉnh sửa và đăng lại</li>
                            </ul>
                        </div>
                    </div>
                    <div class="card border-0 bg-light-subtle mb-4">
                        <div class="card-body p-3">
                            <h6 class="card-title fw-bold mb-3">Thông tin bài đăng</h6>
                            <div class="d-flex mb-2 small text-secondary">
                                <span class="me-auto text-dark">Tiêu đề:</span>
                                <span class="text-end text-dark fw-medium  " id="rejectItemTitle"></span>
                            </div>
                            <div class="d-flex mb-2 small text-secondary">
                                <span class="me-auto text-dark">Loại bài đăng:</span>
                                <span class="text-end text-dark fw-medium badge" id="rejectItemType"></span>
                            </div>
                            <div class="d-flex mb-2 small text-secondary">
                                <span class="me-auto text-dark">Danh mục:</span>
                                <span class="text-end text-dark fw-medium" id="rejectItemCategory"></span>
                            </div>
                            <div class="d-flex mb-2 small text-secondary">
                                <span class="me-auto text-dark">Người đăng:</span>
                                <span class="text-end text-dark fw-medium" id="rejectItemUser"></span>
                            </div>
                            <div class="d-flex mb-0 align-items-center small text-secondary">
                                <span class="me-auto text-dark">Trạng thái hiện tại:</span>
                                <span id="rejectItemStatusBadge"></span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 border border-warning-subtle rounded p-3 bg-warning-subtle">
                        <label for="admin_comment" class="form-label fw-bold text-dark mb-2 small">Lý do từ chối (bắt buộc):</label>
                        <textarea class="form-control border-warning-subtle" id="admin_comment" name="admin_comment" rows="3" placeholder="Nhập lý do từ chối (bắt buộc)..." required></textarea>
                    </div>
                    <input type="hidden" name="status" value="rejected">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-danger">Từ chối</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Xác nhận Xóa -->
<div class="modal fade" id="deleteItemModal" tabindex="-1" aria-labelledby="deleteItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="deleteItemForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteItemModalLabel">Xác nhận xóa bài đăng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-0">
                    <p class="mb-3 text-secondary small">Hành động này không thể hoàn tác</p>
                    <div class="alert alert-danger bg-danger-subtle border-danger-subtle text-danger-emphasis d-flex align-items-start p-3 mb-4 rounded">
                        <i class="fas fa-exclamation-triangle fs-5 me-2" style="color: #dc3545;"></i>
                        <div>
                            <h6 class="alert-heading fw-bold mb-2">Cảnh báo quan trọng</h6>
                            <ul class="mb-0 ps-3 small">
                                <li>Bài đăng sẽ bị xóa vĩnh viễn khỏi hệ thống</li>
                                <li>Tất cả hình ảnh và dữ liệu liên quan sẽ bị xóa</li>
                                <li>Người dùng sẽ nhận được thông báo về việc xóa</li>
                                <li>Không thể khôi phục sau khi xóa</li>
                            </ul>
                        </div>
                    </div>
                    <div class="card border-0 bg-light-subtle mb-4">
                        <div class="card-body p-3">
                            <h6 class="card-title fw-bold mb-3 d-flex align-items-center">
                                <i class="fas fa-file-alt me-2 text-secondary"></i> Thông tin bài đăng
                            </h6>
                            <div class="d-flex mb-2 small text-secondary">
                                <span class="me-auto text-dark">Tiêu đề:</span>
                                <span class="text-end text-dark fw-medium" id="deleteItemTitle"></span>
                            </div>
                            <div class="d-flex mb-2 small text-secondary">
                                <span class="me-auto text-dark">Loại bài đăng:</span>
                                <span class="text-end text-dark fw-medium" id="deleteItemType"></span>
                            </div>
                            <div class="d-flex mb-2 small text-secondary">
                                <span class="me-auto text-dark">Danh mục:</span>
                                <span class="text-end text-dark fw-medium" id="deleteItemCategory"></span>
                            </div>
                            <div class="d-flex mb-2 small text-secondary">
                                <span class="me-auto text-dark">Người đăng:</span>
                                <span class="text-end text-dark fw-medium" id="deleteItemUser"></span>
                            </div>
                            <div class="d-flex mb-0 align-items-center small text-secondary">
                                <span class="me-auto text-dark">Trạng thái hiện tại:</span>
                                <span id="deleteItemStatusBadge"></span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 border border-warning-subtle rounded p-3 bg-warning-subtle">
                        <label for="admin_delete_comment" class="form-label fw-bold text-dark mb-2 d-flex align-items-center small">
                            <i class="fas fa-comment-alt me-2 text-secondary"></i> Lý do xóa bài đăng (bắt buộc):
                        </label>
                        <textarea class="form-control border-warning-subtle" id="admin_delete_comment" name="admin_delete_comment" rows="3" placeholder="Nhập lý do xóa bài đăng (bắt buộc)..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-danger">Xóa bài đăng</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function setupModal(modalId, titleElementId, formId, actionUrlGenerator, extraCallback = null) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const itemId = button.getAttribute('data-item-id');
                    const itemTitle = button.getAttribute('data-item-title');

                    // Set title if selector provided
                    if (titleElementId) {
                        modal.querySelector(titleElementId).textContent = itemTitle;
                    }

                    // Set form action
                    const form = modal.querySelector(formId);
                    form.action = actionUrlGenerator(itemId);

                    // Gọi callback để set các trường khác
                    if (extraCallback) {
                        extraCallback(modal, button);
                    }
                });
            }
        }

        // Helper to render status badge
        function renderStatusBadge(status) {
            switch (status) {
                case 'approved':
                    return '<span class="badge rounded-pill text-success bg-success-subtle py-1 px-2 border border-success-subtle fw-normal">Đã duyệt</span>';
                case 'pending_approval':
                    return '<span class="badge rounded-pill text-warning-emphasis bg-warning-subtle py-1 px-2 border border-warning-subtle fw-normal">Chờ duyệt</span>';
                case 'rejected':
                    return '<span class="badge rounded-pill text-danger bg-danger-subtle py-1 px-2 border border-danger-subtle fw-normal">Từ chối</span>';
                case 'returned':
                    return '<span class="badge rounded-pill text-primary bg-primary-subtle py-1 px-2 border border-primary-subtle fw-normal">Trả lại</span>';
                case 'expired':
                    return '<span class="badge rounded-pill text-secondary bg-secondary-subtle py-1 px-2 border border-secondary-subtle fw-normal">Hết hạn</span>';
                default:
                    return '<span class="badge bg-light text-dark">Không rõ</span>';
            }
        }

        // Map item_type to text
        function getTypeText(type) {
            if (type === 'lost') return 'Mất đồ';
            if (type === 'found') return 'Nhặt được';
            return type;
        }

        // Đảm bảo logic approve giống reject: set đầy đủ các trường
        setupModal(
            'approveItemModal',
            '#approveItemTitle',
            '#approveItemForm',
            (itemId) => `{{ url('admin/items') }}/${itemId}/update-status`,
            function(modal, button) {
                const itemType = button.getAttribute('data-item-type');
                const itemCategory = button.getAttribute('data-item-category');
                const itemUser = button.getAttribute('data-item-user');
                const itemStatus = button.getAttribute('data-item-status');

                modal.querySelector('#approveItemType').textContent = getTypeText(itemType);
                modal.querySelector('#approveItemCategory').textContent = itemCategory;
                modal.querySelector('#approveItemUser').textContent = itemUser;
                modal.querySelector('#approveItemStatusBadge').innerHTML = renderStatusBadge(itemStatus);
            }
        );

        setupModal(
            'rejectItemModal',
            '#rejectItemTitle',
            '#rejectItemForm',
            (itemId) => `{{ url('admin/items') }}/${itemId}/update-status`,
            function(modal, button) {
                const itemType = button.getAttribute('data-item-type');
                const itemCategory = button.getAttribute('data-item-category');
                const itemUser = button.getAttribute('data-item-user');
                const itemStatus = button.getAttribute('data-item-status');

                modal.querySelector('#rejectItemType').textContent = getTypeText(itemType);
                modal.querySelector('#rejectItemCategory').textContent = itemCategory;
                modal.querySelector('#rejectItemUser').textContent = itemUser;
                modal.querySelector('#rejectItemStatusBadge').innerHTML = renderStatusBadge(itemStatus);
            }
        );

        setupModal(
            'deleteItemModal',
            '#deleteItemTitle',
            '#deleteItemForm',
            (itemId) => `{{ url('admin/items') }}/${itemId}`,
            function(modal, button) {
                const itemType = button.getAttribute('data-item-type');
                const itemCategory = button.getAttribute('data-item-category');
                const itemUser = button.getAttribute('data-item-user');
                const itemStatus = button.getAttribute('data-item-status');

                modal.querySelector('#deleteItemType').textContent = getTypeText(itemType);
                modal.querySelector('#deleteItemCategory').textContent = itemCategory;
                modal.querySelector('#deleteItemUser').textContent = itemUser;
                modal.querySelector('#deleteItemStatusBadge').innerHTML = renderStatusBadge(itemStatus);
            }
        );

        // Clear rejection reason when modal is hidden
        const rejectItemModalElement = document.getElementById('rejectItemModal');
        if (rejectItemModalElement) {
            rejectItemModalElement.addEventListener('hidden.bs.modal', function() {
                const textarea = rejectItemModalElement.querySelector('#admin_comment');
                if (textarea) {
                    textarea.value = '';
                }
            });
        }
        // Clear delete reason when modal is hidden
        const deleteItemModalElement = document.getElementById('deleteItemModal');
        if (deleteItemModalElement) {
            deleteItemModalElement.addEventListener('hidden.bs.modal', function() {
                const textarea = deleteItemModalElement.querySelector('#admin_delete_comment');
                if (textarea) {
                    textarea.value = '';
                }
            });
        }
    });
</script>
@endpush