<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Get current user profile
     */
    public function show(Request $request)
    {
        $user = $request->user()->load('role');
        
        return response()->json([
            'id' => $user->id,
            'full_name' => $user->full_name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'photo_url' => $user->photo_url,
            'created_at' => $user->created_at,
            'role' => [
                'id' => $user->role->id,
                'name' => $user->role->name
            ]
        ]);
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'full_name' => [
                'sometimes',
                'string',
                'max:255',
                'regex:/^[a-zA-ZÀ-ỹ\s]+$/u', // Chỉ cho phép chữ cái, dấu tiếng Việt và khoảng trắng
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        // Kiểm tra không chứa số
                        if (preg_match('/\d/', $value)) {
                            $fail('Họ tên không được chứa số.');
                        }
                        
                        // Kiểm tra không chứa ký tự đặc biệt (trừ dấu tiếng Việt và khoảng trắng)
                        if (preg_match('/[^a-zA-ZÀ-ỹ\s]/u', $value)) {
                            $fail('Họ tên không được chứa ký tự đặc biệt.');
                        }
                        
                        // Kiểm tra không có khoảng trắng liên tiếp
                        if (preg_match('/\s{2,}/', $value)) {
                            $fail('Họ tên không được chứa khoảng trắng liên tiếp.');
                        }
                        
                        // Kiểm tra không bắt đầu hoặc kết thúc bằng khoảng trắng
                        if (trim($value) !== $value) {
                            $fail('Họ tên không được bắt đầu hoặc kết thúc bằng khoảng trắng.');
                        }
                    }
                }
            ],
            'phone_number' => [
                'sometimes',
                'string',
                'max:20',
                'unique:users,phone_number,' . $user->id, // Loại trừ user hiện tại khỏi kiểm tra unique
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        // Loại bỏ khoảng trắng và ký tự đặc biệt
                        $cleanPhone = preg_replace('/[^0-9+]/', '', $value);
                        
                        // Định nghĩa các đầu số hợp lệ của các nhà mạng Việt Nam
                        $validPrefixes = [
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
                        $patterns = [
                            '/^0[3-9][0-9]{8}$/', // Format nội địa: 0xx xxxx xxx
                            '/^\+84[3-9][0-9]{8}$/', // Format quốc tế: +84xx xxxx xxx
                            '/^84[3-9][0-9]{8}$/' // Format quốc tế: 84xx xxxx xxx
                        ];
                        
                        $isValid = false;
                        foreach ($patterns as $pattern) {
                            if (preg_match($pattern, $cleanPhone)) {
                                // Kiểm tra thêm đầu số có hợp lệ không
                                if (preg_match('/^0/', $cleanPhone)) {
                                    $prefix = substr($cleanPhone, 0, 2);
                                    if (in_array($prefix, $validPrefixes)) {
                                        $isValid = true;
                                        break;
                                    }
                                } elseif (preg_match('/^\+84/', $cleanPhone)) {
                                    $prefix = substr($cleanPhone, 3, 2);
                                    if (in_array($prefix, $validPrefixes)) {
                                        $isValid = true;
                                        break;
                                    }
                                } elseif (preg_match('/^84/', $cleanPhone)) {
                                    $prefix = substr($cleanPhone, 2, 2);
                                    if (in_array($prefix, $validPrefixes)) {
                                        $isValid = true;
                                        break;
                                    }
                                }
                            }
                        }
                        
                        if (!$isValid) {
                            $fail('Số điện thoại không đúng định dạng. Vui lòng nhập số điện thoại Việt Nam hợp lệ (VD: 0987654321, 0912345678, +84987654321).');
                        }
                    }
                }
            ]
        ], [
            'full_name.string' => 'Họ tên phải là chuỗi ký tự.',
            'full_name.max' => 'Họ tên không được vượt quá 255 ký tự.',
            'full_name.regex' => 'Họ tên chỉ được chứa chữ cái, dấu tiếng Việt và khoảng trắng.',
            
            'phone_number.string' => 'Số điện thoại phải là chuỗi ký tự.',
            'phone_number.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'phone_number.unique' => 'Số điện thoại này đã được sử dụng.',
        ]);
        
        $user->update($validated);
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'photo_url' => $user->photo_url
            ]
        ]);
    }

    /**
     * Update user avatar
     */
    public function updateAvatar(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'avatar' => 'required|image|max:2048' // 2MB max
        ]);
        
        // Delete old avatar if exists
        if ($user->photo_url) {
            \Storage::disk('public')->delete($user->photo_url);
        }
        
        // Store new avatar
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['photo_url' => $path]);
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'photo_url' => $user->photo_url
            ]
        ]);
    }
}
