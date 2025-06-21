<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Hiển thị form đăng nhập.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            // Nếu đã đăng nhập, chuyển hướng tới admin dashboard
            // Sau này sẽ kiểm tra role để chuyển hướng phù hợp nếu có nhiều loại dashboard
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login'); // Sẽ tạo view này ở bước sau
    }

    /**
     * Xử lý yêu cầu đăng nhập.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Thêm điều kiện kiểm tra is_active = true
        $credentials['is_active'] = true;

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // Lấy thông tin user sau khi đăng nhập
            $user = Auth::user();

            // Chỉ cho phép admin (1) hoặc moderator (2) đăng nhập
            if ($user->role_id != 1 && $user->role_id != 2) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'Chỉ quản trị viên hoặc kiểm duyệt viên mới được phép đăng nhập vào hệ thống.',
                ])->onlyInput('email');
            }

            // Nếu là moderator thì chuyển hướng về trang quản lý bài đăng
            if ($user->role_id == 2) {
                return redirect()->intended(route('admin.items.index'));
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng hoặc tài khoản đã bị khóa.',
        ])->onlyInput('email');
    }

    /**
     * Xử lý yêu cầu đăng xuất.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
