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
            <form id="addUserForm" action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <div> {{-- Group title and subtitle --}}
                        <h5 class="modal-title" id="addUserModalLabel">Thêm người dùng mới</h5>
                        <p class="mb-0 text-secondary small">Tạo tài khoản cho người dùng ứng dụng mobile</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-0"> {{-- pb-0 to reduce bottom padding --}}
                    {{-- Error Alert --}}
                    <div class="alert alert-danger d-none" id="addUserErrorAlert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="addUserErrorMessage"></span>
                    </div>
                    
                    <div class="mb-3">
                        <label for="full_name" class="form-label fw-bold text-dark">Tên người dùng <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Nhập tên đầy đủ..." required>
                        <div class="invalid-feedback" id="full_name_error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold text-dark">
                            Email <span class="text-danger">*</span>
                            <i class="fas fa-question-circle text-info ms-1" 
                               data-bs-toggle="tooltip" 
                               data-bs-placement="top" 
                               title="Chỉ chấp nhận email có đuôi @e.tlu.edu.vn hoặc @tlu.edu.vn"></i>
                        </label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="example@e.tlu.edu.vn hoặc example@tlu.edu.vn" required>
                        <div class="form-text small text-muted">Chỉ chấp nhận email có đuôi @e.tlu.edu.vn hoặc @tlu.edu.vn</div>
                        <div class="invalid-feedback" id="email_error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label fw-bold text-dark">
                            Số điện thoại
                            <i class="fas fa-question-circle text-info ms-1" 
                               data-bs-toggle="tooltip" 
                               data-bs-placement="top" 
                               title="Chỉ chấp nhận số điện thoại Việt Nam với đầu số: 03, 05, 07, 08, 09"></i>
                        </label>
                        <input type="tel" class="form-control" id="phone_number" name="phone_number" placeholder="VD: 0987654321, 0912345678, +84987654321">
                        <div class="form-text small text-muted">Nhập số điện thoại Việt Nam (đầu số: 03, 05, 07, 08, 09)</div>
                        <div class="invalid-feedback" id="phone_number_error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold text-dark">
                            Mật khẩu <span class="text-danger">*</span>
                            <i class="fas fa-question-circle text-info ms-1" 
                               data-bs-toggle="tooltip" 
                               data-bs-placement="top" 
                               title="Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt (@$!%*?&)"></i>
                        </label>
                        <div class="input-group"> {{-- For eye icon --}}
                            <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu..." required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye-slash"></i> {{-- Eye icon for visibility toggle --}}
                            </button>
                            <button class="btn btn-outline-info" type="button" id="generatePassword" title="Tạo mật khẩu ngẫu nhiên">
                                <i class="fas fa-dice"></i>
                            </button>
                        </div>
                        <div class="form-text small text-muted">
                            <strong>Yêu cầu mật khẩu:</strong>
                            <ul class="mb-0 mt-1 ps-3">
                                <li id="length-check" class="text-muted"><i class="fas fa-circle text-muted"></i> Ít nhất 8 ký tự</li>
                                <li id="lowercase-check" class="text-muted"><i class="fas fa-circle text-muted"></i> Ít nhất 1 chữ thường</li>
                                <li id="uppercase-check" class="text-muted"><i class="fas fa-circle text-muted"></i> Ít nhất 1 chữ hoa</li>
                                <li id="number-check" class="text-muted"><i class="fas fa-circle text-muted"></i> Ít nhất 1 số</li>
                                <li id="special-check" class="text-muted"><i class="fas fa-circle text-muted"></i> Ít nhất 1 ký tự đặc biệt (@$!%*?&)</li>
                            </ul>
                            <div class="mt-2">
                                <strong>Ví dụ mật khẩu mạnh:</strong> <code>TLU@2024</code>, <code>FindIt@TLU</code>, <code>Student@123</code>
                            </div>
                        </div>
                        <div class="invalid-feedback" id="password_error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="role_id" class="form-label fw-bold text-dark">Vai trò</label>
                        <select class="form-select" id="role_id" name="role_id" required>
                            {{-- Biến $roles cần được truyền từ Controller --}}
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ $role->name == 'App Mobile User' ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="role_id_error"></div>
                    </div>

                    {{-- Important Information Section (Blue Box) --}}
                    <div class="alert alert-info bg-info-subtle border-info-subtle text-info-emphasis d-flex align-items-start p-3 mt-4 mb-4 rounded">
                        <i class="fas fa-info-circle fs-5 me-2" style="color: #0d6efd;"></i> {{-- Bootstrap default info color for icon --}}
                        <div>
                            <h6 class="alert-heading fw-bold mb-2">Thông tin quan trọng</h6>
                            <ul class="mb-0 ps-3 small">
                                <li><strong>Email bắt buộc:</strong> Chỉ chấp nhận email có đuôi @e.tlu.edu.vn hoặc @tlu.edu.vn</li>
                                <li><strong>Mật khẩu mạnh:</strong> Phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt</li>
                                <li><strong>Số điện thoại:</strong> Chỉ chấp nhận số điện thoại Việt Nam (đầu số: 03, 05, 07, 08, 09)</li>
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
                    <button type="submit" class="btn btn-primary" id="addUserSubmitBtn">
                        <span class="spinner-border spinner-border-sm d-none me-2" id="addUserSpinner"></span>
                        Tạo tài khoản
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JavaScript for password toggle --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Khởi tạo tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

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

        // Tạo mật khẩu ngẫu nhiên
        const generatePasswordBtn = document.getElementById('generatePassword');
        if (generatePasswordBtn) {
            generatePasswordBtn.addEventListener('click', function() {
                const generatedPassword = generateStrongPassword();
                password.value = generatedPassword;
                password.setAttribute('type', 'text'); // Hiển thị mật khẩu
                
                // Cập nhật icon eye
                const eyeIcon = togglePassword.querySelector('i');
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
                
                // Validate mật khẩu mới
                validatePassword(generatedPassword);
                
                // Focus vào input
                password.focus();
            });
        }

        function generateStrongPassword() {
            const lowercase = 'abcdefghijklmnopqrstuvwxyz';
            const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            const numbers = '0123456789';
            const special = '@$!%*?&';
            
            let password = '';
            
            // Đảm bảo có ít nhất 1 ký tự từ mỗi loại
            password += lowercase.charAt(Math.floor(Math.random() * lowercase.length));
            password += uppercase.charAt(Math.floor(Math.random() * uppercase.length));
            password += numbers.charAt(Math.floor(Math.random() * numbers.length));
            password += special.charAt(Math.floor(Math.random() * special.length));
            
            // Thêm các ký tự ngẫu nhiên để đủ 8 ký tự
            const allChars = lowercase + uppercase + numbers + special;
            for (let i = 4; i < 12; i++) {
                password += allChars.charAt(Math.floor(Math.random() * allChars.length));
            }
            
            // Xáo trộn mật khẩu
            return password.split('').sort(() => Math.random() - 0.5).join('');
        }

        // Real-time password validation
        const passwordInput = document.getElementById('password');
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                validatePassword(this.value);
            });
        }

        // Real-time phone number validation
        const phoneInput = document.getElementById('phone_number');
        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                validatePhoneNumber(this.value);
            });
        }

        function validatePassword(password) {
            const checks = {
                length: password.length >= 8,
                lowercase: /[a-z]/.test(password),
                uppercase: /[A-Z]/.test(password),
                number: /\d/.test(password),
                special: /[@$!%*?&]/.test(password)
            };

            // Update UI for each check
            updateCheckUI('length-check', checks.length);
            updateCheckUI('lowercase-check', checks.lowercase);
            updateCheckUI('uppercase-check', checks.uppercase);
            updateCheckUI('number-check', checks.number);
            updateCheckUI('special-check', checks.special);
        }

        function validatePhoneNumber(phone) {
            if (!phone) {
                // Nếu trống thì không validate
                phoneInput.classList.remove('is-valid', 'is-invalid');
                return;
            }

            // Loại bỏ khoảng trắng và ký tự đặc biệt
            const cleanPhone = phone.replace(/[^0-9+]/g, '');
            
            // Định nghĩa các đầu số hợp lệ của các nhà mạng Việt Nam
            const validPrefixes = [
                // Viettel
                '03', '05', '07', '08', '09',
                // MobiFone
                '07', '08', '09',
                // Vinaphone
                '03', '05', '08', '09',
                // Vietnamobile
                '05', '08',
                // Gmobile
                '05', '08',
                // Itelecom
                '08'
            ];
            
            // Kiểm tra format số điện thoại Việt Nam
            const patterns = [
                /^0[3-9][0-9]{8}$/, // Format nội địa: 0xx xxxx xxx
                /^\+84[3-9][0-9]{8}$/, // Format quốc tế: +84xx xxxx xxx
                /^84[3-9][0-9]{8}$/ // Format quốc tế: 84xx xxxx xxx
            ];
            
            let isValid = false;
            for (const pattern of patterns) {
                if (pattern.test(cleanPhone)) {
                    // Kiểm tra thêm đầu số có hợp lệ không
                    if (cleanPhone.startsWith('0')) {
                        const prefix = cleanPhone.substring(0, 2);
                        if (validPrefixes.includes(prefix)) {
                            isValid = true;
                            break;
                        }
                    } else if (cleanPhone.startsWith('+84')) {
                        const prefix = cleanPhone.substring(3, 5);
                        if (validPrefixes.includes(prefix)) {
                            isValid = true;
                            break;
                        }
                    } else if (cleanPhone.startsWith('84')) {
                        const prefix = cleanPhone.substring(2, 4);
                        if (validPrefixes.includes(prefix)) {
                            isValid = true;
                            break;
                        }
                    }
                }
            }
            
            if (isValid) {
                phoneInput.classList.remove('is-invalid');
                phoneInput.classList.add('is-valid');
            } else {
                phoneInput.classList.remove('is-valid');
                phoneInput.classList.add('is-invalid');
            }
        }

        function updateCheckUI(elementId, isValid) {
            const element = document.getElementById(elementId);
            if (element) {
                const icon = element.querySelector('i');
                if (isValid) {
                    element.classList.remove('text-muted');
                    element.classList.add('text-success');
                    icon.classList.remove('fa-circle', 'text-muted');
                    icon.classList.add('fa-check-circle', 'text-success');
                } else {
                    element.classList.remove('text-success');
                    element.classList.add('text-muted');
                    icon.classList.remove('fa-check-circle', 'text-success');
                    icon.classList.add('fa-circle', 'text-muted');
                }
            }
        }

        // Xử lý form thêm người dùng với AJAX
        const addUserForm = document.getElementById('addUserForm');
        if (addUserForm) {
            addUserForm.addEventListener('submit', function(event) {
                event.preventDefault();
                
                // Reset validation states
                clearValidationErrors();
                hideErrorAlert();
                
                // Show loading state
                const submitBtn = document.getElementById('addUserSubmitBtn');
                const spinner = document.getElementById('addUserSpinner');
                submitBtn.disabled = true;
                spinner.classList.remove('d-none');
                
                // Get form data
                const formData = new FormData(this);
                
                // Send AJAX request
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success === false) {
                        // Handle validation errors
                        if (data.errors) {
                            showValidationErrors(data.errors);
                        } else {
                            showErrorAlert(data.message);
                        }
                    } else {
                        // Success - redirect to refresh the page
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorAlert('Có lỗi xảy ra khi thêm người dùng. Vui lòng thử lại.');
                })
                .finally(() => {
                    // Reset loading state
                    submitBtn.disabled = false;
                    spinner.classList.add('d-none');
                });
            });
        }

        // Reset form when modal is closed
        const addUserModal = document.getElementById('addUserModal');
        if (addUserModal) {
            addUserModal.addEventListener('hidden.bs.modal', function() {
                const form = this.querySelector('#addUserForm');
                form.reset();
                clearValidationErrors();
                hideErrorAlert();
            });
        }

        // Helper functions
        function clearValidationErrors() {
            const inputs = addUserForm.querySelectorAll('.form-control, .form-select');
            inputs.forEach(input => {
                input.classList.remove('is-invalid');
            });
            
            const errorDivs = addUserForm.querySelectorAll('.invalid-feedback');
            errorDivs.forEach(div => {
                div.textContent = '';
            });
        }

        function showValidationErrors(errors) {
            Object.keys(errors).forEach(field => {
                const input = addUserForm.querySelector(`[name="${field}"]`);
                const errorDiv = addUserForm.querySelector(`#${field}_error`);
                
                if (input && errorDiv) {
                    input.classList.add('is-invalid');
                    errorDiv.textContent = errors[field][0];
                }
            });
        }

        function showErrorAlert(message) {
            const alert = document.getElementById('addUserErrorAlert');
            const messageSpan = document.getElementById('addUserErrorMessage');
            
            if (alert && messageSpan) {
                messageSpan.textContent = message;
                alert.classList.remove('d-none');
                
                // Scroll to top of modal body
                const modalBody = addUserModal.querySelector('.modal-body');
                modalBody.scrollTop = 0;
            }
        }

        function hideErrorAlert() {
            const alert = document.getElementById('addUserErrorAlert');
            if (alert) {
                alert.classList.add('d-none');
            }
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
                        <p class="text-secondary small mb-2">Để xác nhận xóa, vui lòng nhập "xóa" vào ô bên dưới:</p>
                        <input type="text" class="form-control border-warning-subtle" id="confirm_delete_user_input" name="confirm_delete_input" placeholder="Nhập &quot;xóa&quot; để xác nhận" required>
                        <div class="invalid-feedback" id="confirm_delete_feedback">
                            Vui lòng nhập chính xác chữ "xóa" để xác nhận.
                        </div>
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
                const userEmail = button.getAttribute('data-user-email');
                const userPostCount = button.getAttribute('data-user-post-count');
                const form = deleteUserModal.querySelector('#deleteUserForm');
                form.action = `{{ url('admin/users') }}/${userId}`;
                deleteUserModal.querySelector('#deleteUserName').textContent = userName;
                deleteUserModal.querySelector('#deleteUserEmail').textContent = userEmail;
                deleteUserModal.querySelector('#deleteUserPostCount').textContent = userPostCount + ' bài đăng';
                
                // Reset form và validation khi mở modal
                const confirmInput = deleteUserModal.querySelector('#confirm_delete_user_input');
                confirmInput.value = '';
                confirmInput.classList.remove('is-invalid');
            });

            // Xử lý validation khi submit form
            const deleteUserForm = deleteUserModal.querySelector('#deleteUserForm');
            deleteUserForm.addEventListener('submit', function(event) {
                const confirmInput = deleteUserModal.querySelector('#confirm_delete_user_input');
                const inputValue = confirmInput.value.trim();
                
                // Chuẩn hóa chuỗi nhập vào (loại bỏ dấu câu, chuyển về chữ thường)
                const normalizedInput = inputValue.toLowerCase().replace(/[^\w\s]/g, '');
                const expectedValue = 'xoa'; // Giá trị mong đợi sau khi chuẩn hóa
                
                if (normalizedInput !== expectedValue) {
                    event.preventDefault();
                    confirmInput.classList.add('is-invalid');
                    confirmInput.focus();
                    return false;
                }
                
                // Nếu validation pass, remove invalid class
                confirmInput.classList.remove('is-invalid');
            });

            // Xử lý validation real-time khi người dùng nhập
            const confirmInput = deleteUserModal.querySelector('#confirm_delete_user_input');
            confirmInput.addEventListener('input', function() {
                const inputValue = this.value.trim();
                const normalizedInput = inputValue.toLowerCase().replace(/[^\w\s]/g, '');
                const expectedValue = 'xoa';
                
                if (inputValue && normalizedInput !== expectedValue) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        }
    });
</script>
@endpush