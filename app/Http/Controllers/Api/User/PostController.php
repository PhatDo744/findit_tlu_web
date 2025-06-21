<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PostController extends Controller
{
    /**
     * Display a listing of all items (public)
     */
    public function index(Request $request)
    {
        $query = Item::with(['user', 'category', 'images'])
            ->where('status', 'approved') // Chỉ hiển thị bài đã duyệt
            ->where('expiration_date', '>', now()); // Chưa hết hạn

        // Filter by item type
        if ($request->has('item_type')) {
            $query->where('item_type', $request->item_type);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location_description', 'like', "%{$search}%");
            });
        }

        $items = $query->latest()->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => $items->items(),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total()
            ]
        ]);
    }

    /**
     * Get user's own posts
     */
    public function myPosts(Request $request)
    {
        $query = $request->user()->items()->with(['category', 'images']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $items = $query->latest()->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => $items->items(),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total()
            ]
        ]);
    }

    /**
     * Store a newly created item
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location_description' => 'required|string|max:500',
            'item_type' => 'required|in:lost,found',
            'date_lost_or_found' => 'required|date',
            'is_contact_info_public' => 'boolean'
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['status'] = 'pending_approval'; // Needs approval
        $validated['expiration_date'] = Carbon::now()->addDays(14); // 14 days expiration
        $validated['is_contact_info_public'] = $validated['is_contact_info_public'] ?? true;

        $item = Item::create($validated);

        // Gửi thông báo cho user về việc bài đăng đang chờ duyệt
        $request->user()->notify(new \App\Notifications\PostPendingApprovalNotification($item));

        return response()->json([
            'post' => $item->load(['category', 'images'])
        ], 201);
    }

    /**
     * Display the specified item
     */
    public function show($id)
    {
        $item = Item::with(['user', 'category', 'images'])
            ->where('status', '!=', 'pending')
            ->findOrFail($id);

        // Hide contact info if not public
        if (!$item->is_contact_info_public) {
            $item->user->makeHidden(['phone_number', 'email']);
        }

        return response()->json($item);
    }

    /**
     * Update the specified item
     */
    public function update(Request $request, $id)
    {
        $item = $request->user()->items()->findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'sometimes|required|exists:categories,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'location_description' => 'sometimes|required|string|max:500',
            'date_lost_or_found' => 'sometimes|required|date',
            'is_contact_info_public' => 'sometimes|boolean'
        ]);

        // Ghi đè trạng thái về chờ duyệt
        $validated['status'] = 'pending_approval';

        $item->update($validated);

        // Gửi thông báo cho user về việc bài đăng đang chờ duyệt lại
        $request->user()->notify(new \App\Notifications\PostPendingApprovalNotification($item));

        return response()->json([
            'post' => $item->load(['category', 'images'])
        ]);
    }

    /**
     * Remove the specified item
     */
    public function destroy(Request $request, $id)
    {
        $item = $request->user()->items()->findOrFail($id);

        // Delete associated images
        foreach ($item->images as $image) {
            Storage::disk('public')->delete($image->image_url);
        }

        // Xóa vĩnh viễn bài đăng
        $item->forceDelete();
        return response()->noContent();
    }

    /**
     * Mark item as completed
     */
    public function markAsCompleted(Request $request, $id)
    {
        $item = $request->user()->items()->findOrFail($id);
        // Chỉ cho phép đánh dấu hoàn thành nếu đã được admin duyệt
        if ($item->status !== 'approved') {
            return response()->json([
                'message' => 'Bài đăng chưa được admin duyệt, không thể đánh dấu hoàn thành.'
            ], 403);
        }
        $item->update(['status' => 'returned']);
        // Gửi thông báo cho user về việc bài đăng đã hoàn thành
        $request->user()->notify(new \App\Notifications\PostCompletedNotification($item));
        return response()->json([
            'post' => $item->load(['category', 'images'])
        ]);
    }

    /**
     * Upload image for item
     */
    public function uploadImage(Request $request, $id)
    {
        $item = $request->user()->items()->findOrFail($id);

        $request->validate([
            'image' => 'required|image|max:5120', // 5MB max
            'caption' => 'nullable|string|max:255'
        ]);

        $path = $request->file('image')->store('item-images', 'public');

        $image = $item->images()->create([
            'image_url' => $path,
            'caption' => $request->caption
        ]);

        \Log::info('Image record created', ['image_id' => $image->id]);

        // Trả về object đầy đủ, image_url đã là URL đầy đủ nhờ accessor
        return response()->json([
            'id' => $image->id,
            'item_id' => $image->item_id,
            'image_url' => $image->image_url,
            'caption' => $image->caption,
            'created_at' => $image->created_at,
            'updated_at' => $image->updated_at,
        ], 201);
    }

    /**
     * Delete item image
     */
    public function deleteImage(Request $request, $id)
    {
        $image = ItemImage::whereHas('item', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->findOrFail($id);

        Storage::disk('public')->delete($image->image_url);
        $image->delete();

        return response()->noContent();
    }

    /**
     * Lấy danh sách items theo category_id
     */
    public function itemsByCategory(Request $request, $categoryId)
    {
        $query = Item::with(['user', 'category', 'images'])
            ->where('status', 'approved')
            ->where('category_id', $categoryId);

        // Có thể thêm filter khác nếu cần
        $items = $query->latest()->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => $items->items(),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total()
            ]
        ]);
    }

    /**
     * Lấy danh sách items theo item_type (lost/found)
     */
    public function itemsByType(Request $request, $itemType)
    {
        $query = Item::with(['user', 'category', 'images'])
            ->where('status', 'approved')
            ->where('item_type', $itemType);

        $items = $query->latest()->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => $items->items(),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total()
            ]
        ]);
    }

    /**
     * Lấy danh sách items theo category_id và item_type
     */
    public function itemsByCategoryAndType(Request $request, $categoryId, $itemType)
    {
        $query = Item::with(['user', 'category', 'images'])
            ->where('status', 'approved')
            ->where('category_id', $categoryId)
            ->where('item_type', $itemType);

        $items = $query->latest()->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => $items->items(),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total()
            ]
        ]);
    }

    /**
     * Tìm kiếm items theo từ khóa (keyword từ URL)
     */
    public function searchItems(Request $request, $keyword)
    {
        $query = Item::with(['user', 'category', 'images'])
            ->where('status', 'approved')
            ->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%");
             
            });

        $items = $query->latest()->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => $items->items(),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total()
            ]
        ]);
    }
}
