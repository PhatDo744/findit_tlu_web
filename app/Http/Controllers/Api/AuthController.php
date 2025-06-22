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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Str;

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
     * Forgot password - Sends OTP to user's email
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        // Generate OTP
        $otp = random_int(100000, 999999);

        // Store OTP in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $otp, // Storing plain OTP, consider hashing in production
                'created_at' => Carbon::now()
            ]
        );

        // Send OTP to user's email
        try {
            Mail::raw("Your OTP for password reset is: {$otp}", function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Your Password Reset OTP');
            });

            return response()->json([
                'message' => 'An OTP has been sent to your email.'
            ]);
        } catch (\Exception $e) {
            Log::error('Mail Sending Error', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Could not send OTP email. Please try again later.'
            ], 500);
        }
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'token' => 'required|numeric|digits:6', // 'token' is the OTP
            'email' => 'required|email|exists:users,email',
        ]);

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        // Check if OTP record exists
        if (!$resetRecord) {
            throw ValidationException::withMessages([
                'token' => ['Invalid OTP. Please request a new one.'],
            ]);
        }

        // Check if OTP has expired (e.g., 10 minutes validity)
        if (Carbon::parse($resetRecord->created_at)->addMinutes(10)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            throw ValidationException::withMessages([
                'token' => ['The OTP has expired.'],
            ]);
        }

        // Check if OTP is correct
        if ($resetRecord->token !== $request->token) {
            throw ValidationException::withMessages([
                'token' => ['The provided OTP is incorrect.'],
            ]);
        }

        return response()->json([
            'message' => 'OTP verified successfully.'
        ]);
    }

    /**
     * Reset password using OTP
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|numeric|digits:6', // 'token' is the OTP
            'email' => 'required|email|exists:users,email',
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        // Check if OTP record exists
        if (!$resetRecord) {
            throw ValidationException::withMessages([
                'email' => ['Invalid email or OTP request.'],
            ]);
        }

        // Check if OTP has expired (e.g., 10 minutes validity)
        if (Carbon::parse($resetRecord->created_at)->addMinutes(10)->isPast()) {
            // Clean up expired token
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            throw ValidationException::withMessages([
                'token' => ['The OTP has expired.'],
            ]);
        }
        
        // Check if OTP is correct
        // Using non-strict comparison because OTP from request is string, from DB might be int
        if ($resetRecord->token != $request->token) {
            throw ValidationException::withMessages([
                'token' => ['The provided OTP is incorrect.'],
            ]);
        }

        // OTP is valid, proceed with password reset
        $user = User::where('email', $request->email)->first();

        $user->forceFill([
            'password' => Hash::make($request->password)
        ])->save();

        // Revoke all user's tokens
        $user->tokens()->delete();
        
        // Delete the password reset token record
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'message' => 'Password has been reset successfully.'
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
