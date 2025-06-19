<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Findit@TLU Hệ Thống Quản Trị</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-card img {
            width: 80px;
            margin-bottom: 20px;
        }
        .login-card h2 {
            color: #1c3d72; /* TLU Blue */
            margin-bottom: 10px;
            font-size: 1.8rem;
        }
        .login-card p {
            color: #606770;
            margin-bottom: 30px;
            font-size: 0.95rem;
        }
        .form-control {
            border-radius: 6px;
            padding: 12px 15px;
            border: 1px solid #ced4da;
        }
        .form-control:focus {
            border-color: #1c3d72;
            box-shadow: 0 0 0 0.25rem rgba(28, 61, 114, 0.25);
        }
        .btn-primary {
            background-color: #1c3d72;
            border-color: #1c3d72;
            padding: 12px 15px;
            font-size: 1rem;
            border-radius: 6px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #16315a;
            border-color: #16315a;
        }
        .form-check-label {
            font-size: 0.9rem;
            color: #606770;
        }
        .alert-danger {
            font-size: 0.9rem;
        }
         .footer-text {
            margin-top: 30px;
            font-size: 0.8rem;
            color: #6c757d;
        }
        .icon-circle {
            width: 80px;
            height: 80px;
            background-color: #0056b3; /* màu xanh đậm như ảnh */
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px auto;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="icon-circle">
             <i class="fa-solid fa-graduation-cap fa-2x"></i>
        </div>  
        <h2>Findit@TLU</h2>
        <p>Hệ Thống Quản Trị</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3 text-start">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Nhập email của bạn">
            </div>
            <div class="mb-3 text-start">
                <label for="password" class="form-label">Mật khẩu</label>
                <input type="password" class="form-control" id="password" name="password" required placeholder="Nhập mật khẩu">
            </div>
            <div class="mb-3 form-check text-start">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
            </div>
            <button type="submit" class="btn btn-primary w-100">
            <i class="fas fa-sign-in-alt mr-2"></i>Đăng Nhập
            </button>
        </form>
         <p class="footer-text">&copy; {{ date('Y') }} Thủy Lợi University. All rights reserved.</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 