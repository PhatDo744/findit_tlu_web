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
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'phone_number' => 'nullable|string|max:20|unique:users,phone_number',
            'role_id' => 'required|exists:roles,id',
        ]);

        try {
            User::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'role_id' => $request->role_id,
                'is_active' => true,
            ]);
            return redirect()->route('admin.users.index')->with('success', 'Thêm người dùng mới thành công!');
        } catch (\Exception $e) {
            Log::error("Error creating user: " . $e->getMessage());
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
            $action = $newStatus ? 'Mở khóa' : 'Khóa';
            return redirect()->route('admin.users.index')->with('success', "{$action} tài khoản người dùng '{$user->full_name}' thành công!");
        } catch (\Exception $e) {
            Log::error("Error updating user status for user ID {$user->id}: " . $e->getMessage());
            return redirect()->route('admin.users.index')->with('error', 'Có lỗi xảy ra khi cập nhật trạng thái người dùng.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            $userName = $user->full_name;
            $user->delete();
            return redirect()->route('admin.users.index')->with('success', "Xóa tài khoản người dùng '{$userName}' thành công!");
        } catch (\Exception $e) {
            Log::error("Error deleting user ID {$user->id}: " . $e->getMessage());
            return redirect()->route('admin.users.index')->with('error', 'Có lỗi xảy ra khi xóa người dùng.');
        }
    }
}
