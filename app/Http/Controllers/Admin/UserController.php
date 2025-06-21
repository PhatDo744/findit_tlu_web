<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('role')->withCount(['items', 'items as approved_items_count' => function ($q) {
            $q->where('status', 'approved');
        }, 'items as pending_items_count' => function ($q) {
            $q->where('status', 'pending_approval');
        }]);

        if ($request->filled('search_term')) {
            $searchTerm = $request->input('search_term');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('full_name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->input('status') == 'active' ? 1 : 0;
            $query->where('is_active', $status);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'full_name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    'unique:users',
                    function ($attribute, $value, $fail) {
                        $allowedDomains = ['e.tlu.edu.vn', 'tlu.edu.vn'];
                        $emailDomain = substr(strrchr($value, "@"), 1);
                        
                        if (!in_array($emailDomain, $allowedDomains)) {
                            $fail('Email phải có đuôi @e.tlu.edu.vn hoặc @tlu.edu.vn.');
                        }
                    }
                ],
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'max:255',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
                    function ($attribute, $value, $fail) {
                        $errors = [];
                        
                        if (strlen($value) < 8) {
                            $errors[] = 'Mật khẩu phải có ít nhất 8 ký tự';
                        }
                        
                        if (!preg_match('/[a-z]/', $value)) {
                            $errors[] = 'Mật khẩu phải có ít nhất 1 chữ thường';
                        }
                        
                        if (!preg_match('/[A-Z]/', $value)) {
                            $errors[] = 'Mật khẩu phải có ít nhất 1 chữ hoa';
                        }
                        
                        if (!preg_match('/\d/', $value)) {
                            $errors[] = 'Mật khẩu phải có ít nhất 1 số';
                        }
                        
                        if (!preg_match('/[@$!%*?&]/', $value)) {
                            $errors[] = 'Mật khẩu phải có ít nhất 1 ký tự đặc biệt (@$!%*?&)';
                        }
                        
                        if (!empty($errors)) {
                            $fail(implode(', ', $errors));
                        }
                    }
                ],
                'phone_number' => [
                    'nullable',
                    'string',
                    'max:20',
                    'unique:users,phone_number',
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
                ],
                'role_id' => 'required|exists:roles,id',
            ], [
                'full_name.required' => 'Vui lòng nhập tên người dùng.',
                'full_name.string' => 'Tên người dùng phải là chuỗi ký tự.',
                'full_name.max' => 'Tên người dùng không được vượt quá 255 ký tự.',
                
                'email.required' => 'Vui lòng nhập địa chỉ email.',
                'email.string' => 'Email phải là chuỗi ký tự.',
                'email.email' => 'Email không đúng định dạng.',
                'email.max' => 'Email không được vượt quá 255 ký tự.',
                'email.unique' => 'Email này đã được sử dụng.',
                
                'password.required' => 'Vui lòng nhập mật khẩu.',
                'password.string' => 'Mật khẩu phải là chuỗi ký tự.',
                'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
                'password.max' => 'Mật khẩu không được vượt quá 255 ký tự.',
                'password.regex' => 'Mật khẩu không đúng định dạng yêu cầu.',
                
                'phone_number.string' => 'Số điện thoại phải là chuỗi ký tự.',
                'phone_number.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
                'phone_number.unique' => 'Số điện thoại này đã được sử dụng.',
                
                'role_id.required' => 'Vui lòng chọn vai trò.',
                'role_id.exists' => 'Vai trò không tồn tại.',
            ]);

            if ($validator->fails()) {
                throw new \Illuminate\Validation\ValidationException($validator);
            }

            User::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'role_id' => $request->role_id,
                'is_active' => true,
            ]);
            
            // Nếu request là AJAX, trả về JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thêm người dùng mới thành công!'
                ]);
            }
            
            return redirect()->route('admin.users.index')->with('success', 'Thêm người dùng mới thành công!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMsg = collect($e->validator->errors()->all())->implode(' ');
            
            // Nếu request là AJAX, trả về JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMsg ?: 'Vui lòng kiểm tra lại thông tin nhập vào.',
                    'errors' => $e->validator->errors()
                ], 422);
            }
            
            // Nếu không phải AJAX, redirect như cũ
            return redirect()->route('admin.users.index')
                ->with('error_title', 'Lỗi khi thêm người dùng!')
                ->with('error', $errorMsg ?: 'Vui lòng kiểm tra lại thông tin nhập vào.');
        } catch (\Exception $e) {
            Log::error("Error creating user: " . $e->getMessage());
            
            // Nếu request là AJAX, trả về JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi thêm người dùng. Vui lòng thử lại.'
                ], 500);
            }
            
            // Nếu không phải AJAX, redirect như cũ
            return redirect()->route('admin.users.index')->with('error', 'Có lỗi xảy ra khi thêm người dùng. Vui lòng thử lại.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Update the specified user's status (active/inactive).
     */
    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'is_active' => ['required', Rule::in(['0', '1'])],
        ]);

        try {
            $newStatus = (bool)$request->input('is_active');
            $user->is_active = $newStatus;
            $user->save();
            
            if ($newStatus) {
                $title = 'Mở khóa tài khoản thành công!';
                $message = "Người dùng '{$user->full_name}' đã có thể truy cập lại hệ thống.";
            } else {
                $title = 'Khóa tài khoản thành công!';
                $message = "Người dùng '{$user->full_name}' đã bị tạm ngưng truy cập.";
            }

            return redirect()->route('admin.users.index')
                ->with('success_title', $title)
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error("Error updating user status for user ID {$user->id}: " . $e->getMessage());
            return redirect()->route('admin.users.index')->with('error', 'Có lỗi xảy ra khi cập nhật trạng thái người dùng.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user)
    {
        try {
            // Validate confirmation input
            $request->validate([
                'confirm_delete_input' => 'required|string',
            ]);

            $confirmInput = $request->input('confirm_delete_input');
            
            // Chuẩn hóa chuỗi nhập vào (loại bỏ dấu câu, chuyển về chữ thường)
            $normalizedInput = strtolower(preg_replace('/[^\w\s]/', '', $confirmInput));
            $expectedValue = 'xoa'; // Giá trị mong đợi sau khi chuẩn hóa
            
            if ($normalizedInput !== $expectedValue) {
                return redirect()->route('admin.users.index')
                    ->with('error_title', 'Xác nhận xóa không đúng!')
                    ->with('error', 'Vui lòng nhập chính xác chữ "xóa" để xác nhận việc xóa tài khoản.');
            }

            $userName = $user->full_name;
            $user->delete();
            return redirect()->route('admin.users.index')
                ->with('success_title', 'Xóa tài khoản thành công!')
                ->with('success', "Tài khoản của người dùng '{$userName}' đã được xóa vĩnh viễn.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMsg = collect($e->validator->errors()->all())->implode(' ');
            return redirect()->route('admin.users.index')
                ->with('error_title', 'Lỗi xác nhận xóa!')
                ->with('error', $errorMsg ?: 'Vui lòng nhập chính xác chữ "xóa" để xác nhận.');
        } catch (\Exception $e) {
            Log::error("Error deleting user ID {$user->id}: " . $e->getMessage());
            return redirect()->route('admin.users.index')->with('error', 'Có lỗi xảy ra khi xóa người dùng.');
        }
    }
}
