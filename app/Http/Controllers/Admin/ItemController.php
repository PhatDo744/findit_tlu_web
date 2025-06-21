<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Category;
use App\Models\User; // Cần cho thống kê người dùng ở trang show
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Item::with(['user', 'category', 'images']);

        if ($request->filled('search_term')) {
            $searchTerm = $request->input('search_term');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('item_type')) {
            $query->where('item_type', $request->input('item_type'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $items = $query->orderBy('created_at', 'desc')->paginate(10);
        $categories = Category::orderBy('name')->get(); // Để filter

        return view('admin.items.index', compact('items', 'categories'));
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        // Eager load các mối quan hệ cần thiết
        $item->load(['user.role', 'category', 'images']);

        // Lấy thống kê bài đăng của người dùng (nếu có user)
        if ($item->user) {
            $item->user->loadCount(['items', 'items as approved_items_count' => function ($q) {
                $q->where('status', 'approved');
            }, 'items as pending_items_count' => function ($q) {
                $q->where('status', 'pending_approval');
            }/*, 'items as returned_items_count' => function ($q) {
                $q->where('status', 'returned');
            }*/]);
        }

        return view('admin.items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        // Dùng cho trang sửa bài đăng (nếu có). Hiện tại chưa có giao diện cho phần này.
        // $categories = Category::orderBy('name')->get();
        // return view('admin.items.edit', compact('item', 'categories'));
        return redirect()->route('admin.items.index')->with('info', 'Chức năng sửa chi tiết bài đăng đang được phát triển.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        // Logic cho việc admin/mod sửa bài đăng.
        // $request->validate([...]);
        // $item->update($request->all());
        // return redirect()->route('admin.items.show', $item->id)->with('success', 'Bài đăng đã được cập nhật.');
        return redirect()->route('admin.items.index')->with('info', 'Chức năng sửa chi tiết bài đăng đang được phát triển.');
    }

    /**
     * Update the status of the specified item (approve/reject).
     */
    public function updateStatus(Request $request, Item $item)
    {
        $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
            'admin_comment' => [Rule::requiredIf($request->status == 'rejected'), 'nullable', 'string', 'max:1000'],
        ], [
            'admin_comment.required' => 'Vui lòng nhập lý do từ chối bài đăng.'
        ]);

        try {
            $newStatus = $request->input('status');
            $item->status = $newStatus;
            $message = "";
            $title = "";

            if ($newStatus == 'approved') {
                $item->admin_comment = null;
                $title = "Duyệt bài thành công!";
                $message = "Bài đăng đã được duyệt và sẽ được hiển thị công khai.";
                $item->user->notify(new \App\Notifications\PostApprovedNotification($item));
            } elseif ($newStatus == 'rejected') {
                $item->admin_comment = $request->input('admin_comment');
                $title = "Từ chối bài thành công!";
                $message = "Bài đăng đã bị từ chối và người dùng sẽ nhận được thông báo.";
                $item->user->notify(new \App\Notifications\PostRejectedNotification($item, $item->admin_comment));
            }

            $item->save();
            
            $redirectResponse = redirect();
            if (url()->previous() == route('admin.items.show', $item->id)) {
                $redirectResponse = $redirectResponse->route('admin.items.show', $item->id);
            } else {
                $redirectResponse = $redirectResponse->route('admin.items.index');
            }
            return $redirectResponse->with('success_title', $title)->with('success', $message);

        } catch (\Exception $e) {
            Log::error("Error updating item status for item ID {$item->id}: " . $e->getMessage());
            if (url()->previous() == route('admin.items.show', $item->id)) {
                return redirect()->route('admin.items.show', $item->id)->with('error', 'Có lỗi xảy ra khi cập nhật trạng thái bài đăng.');
            }
            return redirect()->route('admin.items.index')->with('error', 'Có lỗi xảy ra khi cập nhật trạng thái bài đăng.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        try {
            $itemTitle = $item->title;
            // TODO: Xóa ảnh liên quan trong storage nếu có
            // foreach ($item->images as $image) { Storage::delete($image->image_url_path_relative_to_storage_disk); }
            $item->delete(); // Sử dụng soft delete nếu model Item dùng SoftDeletes trait
            return redirect()->route('admin.items.index')
                ->with('success_title', 'Xóa bài thành công!')
                ->with('success', "Bài đăng đã được xóa vĩnh viễn khỏi hệ thống.");
        } catch (\Exception $e) {
            Log::error("Error deleting item ID {$item->id}: " . $e->getMessage());
            return redirect()->route('admin.items.index')->with('error', 'Có lỗi xảy ra khi xóa bài đăng.');
        }
    }
}
