@extends('layouts.admin')

@section('title', 'Quản lý Người dùng')

@push('styles')

<style>
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
        object-fit: cover;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }

    .badge-status {
        font-size: 0.8rem;
        padding: 0.4em 0.7em;
    }

    .action-icons a {
        margin-right: 5px;
        color: #6c757d;
    }

    .action-icons a:hover {
        color: #1c3d72;
    }

    .action-icons .btn-delete:hover {
        color: #dc3545;
    }

    .form-filter .form-control,
    .form-filter .form-select {
        font-size: 0.9rem;
    }

    .user-role {
        font-size: 0.8rem;
        color: #6c757d;
    }





    /* Các style cho thead đã nêu ở câu trả lời trước */
    .table-header-blue {
        background-color: #007bff;
        color: white;
    }

    .table-header-blue th {
        padding: 10px;
        text-align: center;
    }

    /* Các style cho tbody (nếu cần) */
    .my-custom-table td {
        padding: 8px;
        /* Ví dụ: thêm padding cho các ô dữ liệu */
        vertical-align: middle;
        /* Căn giữa theo chiều dọc cho dữ liệu */
    }
</style>
@endpush

@section('content')
<div class="container-fluid">


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

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Danh Sách Người Dùng Ứng Dụng</h6>
            <button style="background-color:#1c3d72; color: white;" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-plus-circle me-1 p-2"></i> Thêm Người Dùng
            </button>
        </div>
        <div class="card-body">
            <!-- Bộ lọc -->
            <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4 row gx-3 gy-2 align-items-center form-filter">
                <div class="col-md-5">
                    <label for="search_term" class="visually-hidden">Tìm kiếm</label>
                    <input type="text" class="form-control" name="search_term" id="search_term" placeholder="Tìm kiếm theo tên, email..." value="{{ request('search_term') }}">
                </div>
                <div class="col-md-3">
                    <label for="status" class="visually-hidden">Trạng thái</label>
                    <select class="form-select" name="status" id="status">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Bị khoá</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button style="background-color:#1c3d72; color: white;" type="submit" class="btn w-100"><i class="bi bi-search me-1"></i> Tìm kiếm</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100"><i class="bi bi-arrow-clockwise me-1"></i> Reset</a>
                </div>
            </form>

        </div>
    </div>
    <div class="card shadow">
        <table style=" width: 100%; text-align: center;">
            <thead style="background-color:#1c3d72; color: white;">
                <tr>
                    <th style="padding: 16px;">#</th>
                    <th style="padding: 16px;">HÌNH ẢNH</th>
                    <th style="padding: 16px;">TÊN</th>
                    <th style="padding: 16px;">EMAIL</th>
                    <th style="padding: 16px;">SỐ ĐIỆN THOẠI</th>
                    <th style="padding: 16px;">TRẠNG THÁI</th>
                    <th style="padding: 16px;">SỐ BÀI ĐĂNG</th>
                    <th style="padding: 16px;">NGÀY TẠO</th>
                    <th style="padding: 16px;">HÀNH ĐỘNG</th>
                </tr>
            </thead>
            <tbody>
                {{-- Dữ liệu bảng của bạn ở đây --}}
                @forelse ($users as $user)
                <tr>
                    <td class=" text-center align-middle">{{ $user->id }}</td>
                    <td class="text-center align-middle">
                        <img src="{{ $user->photo_url ?? '/images/default_avatar.png' }}" alt="avatar" class="user-avatar">
                    </td>
                    <td class="text-center align-middle">
                        {{ $user->full_name }}
                    </td>
                    <td class="text-center align-middle">{{ $user->email }}</td>
                    <td class="text-center align-middle">{{ $user->phone_number ?? '-' }}</td>
                    <td class="text-center align-middle">
                        @if ($user->is_active)
                        <span class="badge bg-success-subtle text-success badge-status">Hoạt động</span>
                        @else
                        <span class="badge bg-danger-subtle text-danger badge-status">Bị khoá</span>
                        @endif
                    </td>
                    <td class="text-center align-middle" style="font-size: 0.85rem;">
                        <span>Tổng: {{ $user->items_count ?? 0 }}</span> <br>
                        <span class="text-success"> Đã duyệt: {{ $user->approved_items_count ?? 0 }}</span> <br>
                        <span class="text-warning">Chờ duyệt: {{ $user->pending_items_count ?? 0 }}</span>
                    </td>
                    <td class="text-center align-middle">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td class="action-icons  text-center align-middle" style="padding: 40px 0;">
                        @if ($user->is_active)
                        <a href="#" class="btn btn-action-icon btn-action-warning bg-warning-subtle"
                            data-bs-toggle="modal" data-bs-target="#lockUserModal"
                            data-user-id="{{ $user->id }}" data-user-name="{{ $user->full_name }}" data-user-email="{{ $user->email }}" title="Khóa tài khoản">
                            <i class="bi bi-lock text-warning"></i>
                        </a>
                        @else
                        <a href="#" class="btn btn-action-icon btn-action-success bg-success-subtle"
                            data-bs-toggle="modal" data-bs-target="#unlockUserModal"
                            data-user-id="{{ $user->id }}" data-user-name="{{ $user->full_name }}" data-user-email="{{ $user->email }}" title="Mở khóa tài khoản">
                            <i class="bi bi-unlock text-success"></i>
                        </a>
                        @endif
                        <a href="#" class="btn btn-action-icon btn-action-danger bg-danger-subtle"
                            data-bs-toggle="modal" data-bs-target="#deleteUserModal"
                            data-user-id="{{ $user->id }}"
                            data-user-name="{{ $user->full_name }}"
                            data-user-email="{{ $user->email }}"
                            data-user-post-count="{{ $user->items_count ?? 0 }}"
                            title="Xóa tài khoản">
                            <i class="bi bi-trash text-danger"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">Không tìm thấy người dùng nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>
    <!-- Phân trang -->
    @if ($users->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <small>Hiển thị {{ $users->firstItem() }} đến {{ $users->lastItem() }} của {{ $users->total() }} kết quả</small>
        {{ $users->appends(request()->query())->links() }}
    </div>
    @endif

</div>

<!-- Modal Thêm Người Dùng Mới -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <div> {{-- Group title and subtitle --}}
                        <h5 class="modal-title" id="addUserModalLabel">Thêm người dùng mới</h5>
                        <p class="mb-0 text-secondary small">Tạo tài khoản cho người dùng ứng dụng mobile</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-0"> {{-- pb-0 to reduce bottom padding --}}
                    <div class="mb-3">
                        <label for="full_name" class="form-label fw-bold text-dark">Tên người dùng <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Nhập tên đầy đủ..." required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold text-dark">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="example@tlu.edu.vn" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label fw-bold text-dark">Số điện thoại</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Nhập số điện thoại...">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold text-dark">Mật khẩu <span class="text-danger">*</span></label>
                        <div class="input-group"> {{-- For eye icon --}}
                            <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu..." required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye-slash"></i> {{-- Eye icon for visibility toggle --}}
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="role_id" class="form-label fw-bold text-dark">Vai trò</label>
                        <select class="form-select" id="role_id" name="role_id" required>
                            {{-- Biến $roles cần được truyền từ Controller --}}
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ $role->name == 'App Mobile User' ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Important Information Section (Blue Box) --}}
                    <div class="alert alert-info bg-info-subtle border-info-subtle text-info-emphasis d-flex align-items-start p-3 mt-4 mb-4 rounded">
                        <i class="fas fa-info-circle fs-5 me-2" style="color: #0d6efd;"></i> {{-- Bootstrap default info color for icon --}}
                        <div>
                            <h6 class="alert-heading fw-bold mb-2">Thông tin quan trọng</h6>
                            <ul class="mb-0 ps-3 small">
                                <li>Người dùng sẽ nhận email kích hoạt tài khoản</li>
                                <li>Mật khẩu tạm thời sẽ được gửi qua email</li>
                                <li>Tài khoản sẽ ở trạng thái hoạt động sau khi tạo</li>
                                <li>Người dùng có thể đổi mật khẩu sau lần đăng nhập đầu</li>
                            </ul>
                        </div>
                    </div>

                    <p class="text-muted small mt-3 mb-0">Các trường có dấu (*) là bắt buộc</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary">Tạo tài khoản</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JavaScript for password toggle --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        if (togglePassword && password) {
            togglePassword.addEventListener('click', function() {
                // Toggle the type attribute
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);

                // Toggle the icon
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }
    });
</script>

<!-- Modal Khóa Tài Khoản -->
<div class="modal fade" id="lockUserModal" tabindex="-1" aria-labelledby="lockUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="lockUserForm" method="POST" action="">
                @csrf
                <div class="modal-header">
                    <div> {{-- Group title and subtitle --}}
                        <h5 class="modal-title" id="lockUserModalLabel">Xác nhận khóa tài khoản</h5>
                        <p class="mb-0 text-secondary small">Người dùng sẽ không thể đăng nhập vào hệ thống</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-0"> {{-- pb-0 to reduce bottom padding --}}

                    {{-- Important Warning Section (Red Box) --}}
                    <div class="alert alert-danger bg-danger-subtle border-danger-subtle text-danger-emphasis d-flex align-items-start p-3 mb-4 rounded">
                        <i class="fas fa-exclamation-triangle fs-5 me-2" style="color: #dc3545;"></i> {{-- Bootstrap default danger color for icon --}}
                        <div>
                            <h6 class="alert-heading fw-bold mb-2">Cảnh báo quan trọng</h6>
                            <ul class="mb-0 ps-3 small">
                                <li>Tài khoản sẽ bị khóa và không thể đăng nhập</li>
                                <li>Người dùng sẽ nhận được thông báo về việc khóa tài khoản</li>
                                <li>Hành động này có thể được hoàn tác sau này</li>
                                <li>Tất cả bài đăng của người dùng vẫn tồn tại</li>
                            </ul>
                        </div>
                    </div>

                    {{-- User Information Section (Grey Box) --}}
                    <div class="card border-0 bg-light-subtle mb-4">
                        <div class="card-body p-3">
                            <h6 class="card-title fw-bold mb-3 d-flex align-items-center">
                                <i class="fas fa-user me-2 text-secondary"></i> Thông tin người dùng
                            </h6>
                            <div class="d-flex mb-2 small text-secondary">
                                <span class="me-auto text-dark">Tên người dùng:</span>
                                <span class="text-end text-dark fw-medium" id="lockUserName">Isabell Rohan</span> {{-- Dynamic content --}}
                            </div>
                            <div class="d-flex mb-2 small text-secondary">
                                <span class="me-auto text-dark">Email:</span>
                                <span class="text-end text-dark fw-medium" id="lockUserEmail">uokuneva@e.tlu.edu.vn</span> {{-- Dynamic content --}}
                            </div>
                            <div class="d-flex mb-0 align-items-center small text-secondary">
                                <span class="me-auto text-dark">Trạng thái hiện tại:</span>
                                <span class="badge rounded-pill text-danger-emphasis bg-danger-subtle py-1 px-2 border border-danger-subtle fw-normal" id="lockUserStatusPill">Hoạt động</span> {{-- Dynamic content, assuming "Hoạt động" before locking --}}
                            </div>
                        </div>
                    </div>

                    {{-- Reason for Locking Section (Orange Border Textarea) --}}
                    <div class="mb-3 border border-warning-subtle rounded p-3 bg-warning-subtle">
                        <label for="admin_lock_comment" class="form-label fw-bold text-dark mb-2 d-flex align-items-center small">
                            <i class="fas fa-comment-alt me-2 text-secondary"></i> Lý do khóa tài khoản (bắt buộc):
                        </label>
                        <textarea class="form-control border-warning-subtle" id="admin_lock_comment" name="admin_lock_comment" rows="3" placeholder="Nhập lý do khóa tài khoản (bắt buộc)..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-danger">Khóa tài khoản</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Mở Khóa Tài Khoản -->
<div class="modal fade" id="unlockUserModal" tabindex="-1" aria-labelledby="unlockUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="unlockUserForm" method="POST" action="">
                @csrf
                <div class="modal-header">
                    <div> {{-- Group title and subtitle --}}
                        <h5 class="modal-title" id="unlockUserModalLabel">Xác nhận mở khóa tài khoản</h5>
                        <p class="mb-0 text-secondary small">Người dùng sẽ có thể truy cập lại ứng dụng</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-0"> {{-- pb-0 to reduce bottom padding --}}

                    {{-- Unlock Information Section (Green Box) --}}
                    <div class="alert alert-success bg-success-subtle border-success-subtle text-success-emphasis d-flex align-items-start p-3 mb-4 rounded">
                        <i class="fas fa-unlock fs-5 me-2" style="color: #198754;"></i> {{-- Specific unlock icon --}}
                        <div>
                            <h6 class="alert-heading fw-bold mb-2">Thông tin mở khóa</h6>
                            <ul class="mb-0 ps-3 small">
                                <li>Người dùng sẽ có thể đăng nhập vào ứng dụng</li>
                                <li>Tài khoản sẽ được khôi phục đầy đủ quyền truy cập</li>
                                <li>Người dùng sẽ nhận được thông báo qua email</li>
                                <li>Có thể tạo và quản lý bài đăng như bình thường</li>
                            </ul>
                        </div>
                    </div>

                    {{-- User Information Section (Grey Box) --}}
                    <div class="card border-0 bg-light-subtle mb-4">
                        <div class="card-body p-3">
                            <h6 class="card-title fw-bold mb-3 d-flex align-items-center">
                                <i class="fas fa-user me-2 text-secondary"></i> Thông tin người dùng
                            </h6>
                            <div class="d-flex mb-2 small text-secondary">
                                <span class="me-auto text-dark">Tên người dùng:</span>
                                <span class="text-end text-dark fw-medium" id="unlockUserName">Isabell Rohan</span> {{-- Dynamic content --}}
                            </div>
                            <div class="d-flex mb-2 small text-secondary">
                                <span class="me-auto text-dark">Email:</span>
                                <span class="text-end text-dark fw-medium" id="unlockUserEmail">uokuneva@e.tlu.edu.vn</span> {{-- Dynamic content --}}
                            </div>
                            <div class="d-flex mb-0 align-items-center small text-secondary">
                                <span class="me-auto text-dark">Trạng thái hiện tại:</span>
                                <span class="text-danger fw-medium" id="unlockUserStatus">Bị khóa</span> {{-- Dynamic content, no pill as per image --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button> {{-- Changed to "Hủy bỏ" --}}
                    <button type="submit" class="btn btn-success">Mở khóa</button> {{-- Changed to "Mở khóa" --}}
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Xóa Tài Khoản -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="deleteUserForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <div> {{-- Group title and subtitle --}}
                        <h5 class="modal-title" id="deleteUserModalLabel">Xác nhận xóa tài khoản</h5>
                        <p class="mb-0 text-secondary small">Hành động này không thể hoàn tác</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-0"> {{-- pb-0 to reduce bottom padding --}}

                    {{-- Important Warning Section (Red Box) --}}
                    <div class="alert alert-danger bg-danger-subtle border-danger-subtle text-danger-emphasis d-flex align-items-start p-3 mb-4 rounded">
                        <i class="fas fa-exclamation-triangle fs-5 me-2" style="color: #dc3545;"></i> {{-- Bootstrap default danger color for icon --}}
                        <div>
                            <h6 class="alert-heading fw-bold mb-2">Cảnh báo quan trọng</h6>
                            <ul class="mb-0 ps-3 small">
                                <li>Tài khoản sẽ bị xóa vĩnh viễn khỏi hệ thống</li>
                                <li>Tất cả bài đăng của người dùng sẽ bị xóa</li>
                                <li>Dữ liệu không thể khôi phục sau khi xóa</li>
                                <li>Người dùng sẽ không thể đăng nhập lại</li>
                            </ul>
                        </div>
                    </div>

                    {{-- User Information Section (Grey Box) --}}
                    <div class="card border-0 bg-light-subtle mb-4">
                        <div class="card-body p-3">
                            <h6 class="card-title fw-bold mb-3 d-flex align-items-center">
                                <i class="fas fa-user me-2 text-secondary"></i> Thông tin người dùng
                            </h6>
                            <div class="d-flex mb-2 small text-secondary">
                                <span class="me-auto text-dark">Tên người dùng:</span>
                                <span class="text-end text-dark fw-medium" id="deleteUserName">Isabell Rohan</span> {{-- Dynamic content --}}
                            </div>
                            <div class="d-flex mb-2 small text-secondary">
                                <span class="me-auto text-dark">Email:</span>
                                <span class="text-end text-dark fw-medium" id="deleteUserEmail">uokuneva@e.tlu.edu.vn</span> {{-- Dynamic content --}}
                            </div>
                            <div class="d-flex mb-0 small text-secondary">
                                <span class="me-auto text-dark">Số bài đăng:</span>
                                <span class="text-end text-dark fw-medium" id="deleteUserPostCount">2 bài đăng</span> {{-- Dynamic content --}}
                            </div>
                        </div>
                    </div>

                    {{-- Confirmation Input Section (Orange Box) --}}
                    <div class="mb-3 border border-warning-subtle rounded p-3 bg-warning-subtle">
                        <label for="confirm_delete_user_input" class="form-label fw-bold text-dark mb-2 d-flex align-items-center small">
                            <i class="fas fa-exclamation-circle me-2 text-warning"></i> Xác nhận xóa
                        </label>
                        <p class="text-secondary small mb-2">Để xác nhận xóa, vui lòng nhập "XÓA" vào ô bên dưới:</p>
                        <input type="text" class="form-control border-warning-subtle" id="confirm_delete_user_input" placeholder="Nhập &quot;XÓA&quot; để xác nhận" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button> {{-- Changed to "Hủy bỏ" --}}
                    <button type="submit" class="btn btn-danger">Xóa</button> {{-- Kept "Xóa" as per image --}}
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý modal khóa tài khoản
        const lockUserModal = document.getElementById('lockUserModal');
        if (lockUserModal) {
            lockUserModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const userId = button.getAttribute('data-user-id');
                const userName = button.getAttribute('data-user-name');
                const form = lockUserModal.querySelector('#lockUserForm');
                form.action = `{{ url('admin/users') }}/${userId}/update-status`;
                lockUserModal.querySelector('#lockUserName').textContent = userName;
                // Thêm input hidden để gửi trạng thái is_active = false
                let statusInput = form.querySelector('input[name="is_active"]');
                if (!statusInput) {
                    statusInput = document.createElement('input');
                    statusInput.type = 'hidden';
                    statusInput.name = 'is_active';
                    form.appendChild(statusInput);
                }
                statusInput.value = '0'; // Giá trị cho "bị khóa"
            });
        }

        // Xử lý modal mở khóa tài khoản
        const unlockUserModal = document.getElementById('unlockUserModal');
        if (unlockUserModal) {
            unlockUserModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const userId = button.getAttribute('data-user-id');
                const userName = button.getAttribute('data-user-name');
                const form = unlockUserModal.querySelector('#unlockUserForm');
                form.action = `{{ url('admin/users') }}/${userId}/update-status`;
                unlockUserModal.querySelector('#unlockUserName').textContent = userName;
                let statusInput = form.querySelector('input[name="is_active"]');
                if (!statusInput) {
                    statusInput = document.createElement('input');
                    statusInput.type = 'hidden';
                    statusInput.name = 'is_active';
                    form.appendChild(statusInput);
                }
                statusInput.value = '1'; // Giá trị cho "hoạt động"
            });
        }

        // Xử lý modal xóa tài khoản
        const deleteUserModal = document.getElementById('deleteUserModal');
        if (deleteUserModal) {
            deleteUserModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const userId = button.getAttribute('data-user-id');
                const userName = button.getAttribute('data-user-name');
                const form = deleteUserModal.querySelector('#deleteUserForm');
                form.action = `{{ url('admin/users') }}/${userId}`;
                deleteUserModal.querySelector('#deleteUserName').textContent = userName;
            });
        }
    });
</script>
@endpush