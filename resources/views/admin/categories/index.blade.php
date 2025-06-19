@extends('layouts.admin')

@section('title', 'Quản lý Danh mục')

@push('styles')
<style>
    .action-icons a {
        margin-right: 8px;
        color: #6c757d;
        font-size: 1.1rem;
    }

    .action-icons a.btn-edit:hover {
        color: #0d6efd;
        /* Bootstrap primary */
    }

    .action-icons a.btn-delete:hover {
        color: #dc3545;
        /* Bootstrap danger */
    }

    .table th,
    .table td {
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 page-title">Quản lý Danh mục</h1>
    <p class="mb-4">Thêm, sửa, xóa các danh mục đồ vật (ví dụ: Điện tử, Giấy tờ, Ví/Túi).</p>

    <!-- Thông báo Flash -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif


    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách Danh mục</h6>
            <button style="background-color: #1c3d72; color:white" class="btn text-light btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="bi bi-plus-circle me-1"></i> Thêm Danh Mục
            </button>
        </div>
        <div class="card-body">

            <table style=" width: 100%; ;">
                <thead style="background-color: #1c3d72; color:white">
                    <tr>
                        <th style="padding:16px; width: 10%; " class="text-center">#</th>
                        <th style="padding:16px 0">Tên Danh Mục</th>
                        <th style="padding:16px; width: 15%;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $index => $category)
                    <tr>
                        <td class=" text-center">{{ $categories->firstItem() + $index }}</td>
                        <td>{{ $category->name }}</td>
                        <td style="padding: 16px;" class=" action-icons  gap-2"> {{-- Added flex utilities for consistent spacing and alignment --}}
                            <a href="#" class="btn btn-action-icon btn-action-secondary bg-warning-subtle"
                                data-bs-toggle="modal" data-bs-target="#editCategoryModal"
                                data-id="{{ $category->id }}"
                                data-name="{{ $category->name }}"
                                data-post-count="{{ $category->items_count ?? 0 }}"
                                data-created-at="{{ optional($category->created_at)->format('d/m/Y') }}"
                                data-updated-at="{{ optional($category->updated_at)->format('d/m/Y') }}"
                                title="Sửa danh mục">
                                <i class="bi bi-pencil text-warning"></i> {{-- Changed to outline pencil icon and applied text-secondary --}}
                            </a>
                            <a href="#" class="btn btn-action-icon btn-action-danger bg-danger-subtle"
                                data-bs-toggle="modal" data-bs-target="#deleteCategoryModal"
                                data-id="{{ $category->id }}"
                                data-name="{{ $category->name }}"
                                data-post-count="{{ $category->items_count ?? 0 }}"
                                data-created-at="{{ optional($category->created_at)->format('d/m/Y') }}"
                                title="Xóa danh mục">
                                <i class="bi bi-trash text-danger"></i> {{-- Changed to outline trash icon and applied text-danger --}}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">Chưa có danh mục nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>


            <!-- Phân trang -->
            @if ($categories->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <small>Hiển thị {{ $categories->firstItem() }} đến {{ $categories->lastItem() }} của {{ $categories->total() }} kết quả</small>
                {{ $categories->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Thêm Danh Mục -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <div> {{-- Use a div to group title and subtitle --}}
                        <h5 class="modal-title" id="addCategoryModalLabel">Thêm danh mục mới</h5>
                        <p class="mb-0 text-secondary small">Tạo danh mục cho bài đăng tìm đồ</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-0"> {{-- pb-0 to reduce bottom padding if needed --}}
                    <div class="mb-3">
                        <label for="add_category_name" class="form-label fw-bold text-dark">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add_category_name" name="name" required placeholder="Nhập tên danh mục...">
                    </div>

                    {{-- Important Information Section (Blue Box) --}}
                    <div class="alert alert-info bg-info-subtle border-info-subtle text-info-emphasis d-flex align-items-start p-3 mb-4 rounded">
                        <i class="fas fa-info-circle fs-5 me-2" style="color: #0d6efd;"></i> {{-- Bootstrap default info color for icon --}}
                        <div>
                            <h6 class="alert-heading fw-bold mb-2">Thông tin quan trọng</h6>
                            <ul class="mb-0 ps-3 small">
                                <li>Danh mục sẽ hiển thị ngay lập tức cho người dùng</li>
                                <li>Tên danh mục nên ngắn gọn và dễ hiểu</li>
                                <li>Màu sắc giúp phân biệt các danh mục khác nhau</li>
                                <li>Có thể chỉnh sửa thông tin sau khi tạo</li>
                            </ul>
                        </div>
                    </div>

                    {{-- Required Fields Note --}}
                    <p class="text-danger small mb-3">Các trường có dấu (*) là bắt buộc</p>

                    {{-- Removed optional description field as it's not in the image --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary">Tạo danh mục</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sửa Danh Mục -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editCategoryForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="editCategoryModalLabel">Chỉnh sửa danh mục</h5>
                        <p class="mb-0 text-secondary small">Cập nhật thông tin danh mục bài đăng</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-0">
                    <input type="hidden" id="edit_category_id" name="id">
                    <div class="mb-3">
                        <label for="edit_category_name" class="form-label fw-bold text-dark">Tên danh mục</label>
                        <input type="text" class="form-control" id="edit_category_name" name="name" required>
                    </div>
                    <div class="alert alert-info bg-info-subtle border-info-subtle text-info-emphasis d-flex align-items-start p-3 mb-4 rounded">
                        <i class="fas fa-info-circle fs-5 me-2" style="color: #0d6efd;"></i>
                        <div>
                            <h6 class="alert-heading fw-bold mb-2">Thông tin hiện tại</h6>
                            <div class="d-flex mb-1 small text-secondary">
                                <span class="me-auto text-dark">Số bài đăng:</span>
                                <span class="text-end text-dark fw-medium" id="editCategoryPostCount"></span>
                            </div>
                            <div class="d-flex mb-1 small text-secondary">
                                <span class="me-auto text-dark">Ngày tạo:</span>
                                <span class="text-end text-dark fw-medium" id="editCategoryCreatedAt"></span>
                            </div>
                            <div class="d-flex mb-0 small text-secondary">
                                <span class="me-auto text-dark">Cập nhật lần cuối:</span>
                                <span class="text-end text-dark fw-medium" id="editCategoryUpdatedAt"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Xóa Danh Mục -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="deleteCategoryForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="deleteCategoryModalLabel">Xác nhận xóa danh mục</h5>
                        <p class="mb-0 text-secondary small">Hành động này không thể hoàn tác</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-0">
                    <div class="alert alert-danger bg-danger-subtle border-danger-subtle text-danger-emphasis d-flex align-items-start p-3 mb-4 rounded">
                        <i class="fas fa-exclamation-triangle fs-5 me-2" style="color: #dc3545;"></i>
                        <div>
                            <h6 class="alert-heading fw-bold mb-2">Cảnh báo quan trọng</h6>
                            <ul class="mb-0 ps-3 small">
                                <li>Danh mục sẽ bị xóa vĩnh viễn khỏi hệ thống</li>
                                <li>Tất cả bài đăng thuộc danh mục này sẽ chuyển về "Khác"</li>
                                <li>Dữ liệu không thể khôi phục sau khi xóa</li>
                                <li>Người dùng sẽ không thể chọn danh mục này nữa</li>
                            </ul>
                        </div>
                    </div>
                    <div class="card border-0 bg-light-subtle mb-4">
                        <div class="card-body p-3">
                            <h6 class="card-title fw-bold mb-3 d-flex align-items-center">
                                <i class="fas fa-file-alt me-2 text-secondary"></i> Thông tin danh mục
                            </h6>
                            <div class="d-flex mb-2 small text-secondary">
                                <span class="me-auto text-dark">Tên danh mục:</span>
                                <span class="text-end text-dark fw-medium" id="deleteCategoryNameDisplay"></span>
                            </div>
                            <div class="d-flex mb-2 small text-secondary">
                                <span class="me-auto text-dark">Số bài đăng hiện tại:</span>
                                <span class="text-end text-dark fw-medium" id="deleteCategoryPostCount"></span>
                            </div>
                            <div class="d-flex mb-0 small text-secondary">
                                <span class="me-auto text-dark">Ngày tạo:</span>
                                <span class="text-end text-dark fw-medium" id="deleteCategoryCreatedAt"></span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 border border-warning-subtle rounded p-3 bg-warning-subtle">
                        <label for="confirm_delete_input" class="form-label fw-bold text-dark mb-2 d-flex align-items-center small">
                            <i class="fas fa-exclamation-circle me-2 text-warning"></i> Xác nhận xóa
                        </label>
                        <p class="text-secondary small mb-2">Vui lòng nhập "XÓA" để xác nhận hành động này:</p>
                        <input type="text" class="form-control border-warning-subtle" id="confirm_delete_input" placeholder="Nhập &quot;XÓA&quot; để xác nhận..." required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sửa danh mục: lấy dữ liệu động từ data-*
        const editCategoryModal = document.getElementById('editCategoryModal');
        if (editCategoryModal) {
            editCategoryModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const categoryId = button.getAttribute('data-id');
                const categoryName = button.getAttribute('data-name');
                // Các thuộc tính động mới
                const postCount = button.getAttribute('data-post-count') || '';
                const createdAt = button.getAttribute('data-created-at') || '';
                const updatedAt = button.getAttribute('data-updated-at') || '';

                const form = editCategoryModal.querySelector('#editCategoryForm');
                form.action = `{{ url('admin/categories') }}/${categoryId}`;
                editCategoryModal.querySelector('#edit_category_id').value = categoryId;
                editCategoryModal.querySelector('#edit_category_name').value = categoryName;
                editCategoryModal.querySelector('#editCategoryPostCount').textContent = postCount;
                editCategoryModal.querySelector('#editCategoryCreatedAt').textContent = createdAt;
                editCategoryModal.querySelector('#editCategoryUpdatedAt').textContent = updatedAt;
            });
        }

        // Xóa danh mục: lấy dữ liệu động từ data-*
        const deleteCategoryModal = document.getElementById('deleteCategoryModal');
        if (deleteCategoryModal) {
            deleteCategoryModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const categoryId = button.getAttribute('data-id');
                const categoryName = button.getAttribute('data-name');
                const postCount = button.getAttribute('data-post-count') || '';
                const createdAt = button.getAttribute('data-created-at') || '';

                const form = deleteCategoryModal.querySelector('#deleteCategoryForm');
                form.action = `{{ url('admin/categories') }}/${categoryId}`;
                deleteCategoryModal.querySelector('#deleteCategoryNameDisplay').textContent = categoryName;
                deleteCategoryModal.querySelector('#deleteCategoryPostCount').textContent = postCount;
                deleteCategoryModal.querySelector('#deleteCategoryCreatedAt').textContent = createdAt;
            });
        }
    });
</script>
@endpush