<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login
     */
    public function login(Request $request)
    {
        try {
            // Validate đầu vào
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            // Tìm user theo email
            $user = User::where('email', $request->email)->first();

            // Kiểm tra thông tin đăng nhập
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'The provided credentials are incorrect.'
                ], 401);
            }

            // Kiểm tra tài khoản có bị khóa không
            if (!$user->is_active) {
                return response()->json([
                    'message' => 'Your account has been deactivated.'
                ], 403);
            }

            // Tạo token
            $token = $user->createToken('auth-token')->plainTextToken;

            // Trả về dữ liệu JSON
            return response()->json([
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'phone_number' => $user->phone_number,
                    'role_id' => $user->role_id,
                    'is_active' => $user->is_active
                ]
            ]);
        } catch (ValidationException $e) {
            // Trả về lỗi validate rõ ràng
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Log lỗi và trả về lỗi hệ thống
            Log::error('Login Error', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage() // Có thể ẩn dòng này ở production
            ], 500);
        }
    }

    /**
     * Register
     */

    public function register(Request $request)
    {
        try {
            // Validate dữ liệu đầu vào
            $validatedData = $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]*tlu\\.edu\\.vn$/',
                'password' => ['required', 'confirmed', PasswordRule::defaults()],
                'phone_number' => 'required|string|max:20'
            ], [
                'email.regex' => 'Email must be a valid TLU email address'
            ]);

            // Tạo user mới
            $user = User::create([
                'full_name' => $validatedData['full_name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'phone_number' => $validatedData['phone_number'],
                'role_id' => 3, // Default: user
                'is_active' => true
            ]);

            // Tạo token
            $token = $user->createToken('auth-token')->plainTextToken;

            // Trả về JSON
            return response()->json([
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'phone_number' => $user->phone_number,
                    'role_id' => $user->role_id,
                    'is_active' => $user->is_active
                ]
            ], 201);
        } catch (ValidationException $e) {
            // Trả về lỗi validate rõ ràng
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Log lỗi và trả về lỗi hệ thống
            Log::error('Register Error', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage() // Chỉ hiển thị khi debug
            ], 500);
        }
    }
    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Forgot password
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Reset link sent to your email'
            ]);
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();

                // Revoke all tokens
                $user->tokens()->delete();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password has been reset successfully'
            ]);
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
