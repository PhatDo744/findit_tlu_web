<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount('items')->orderBy('name')->paginate(10);
        return view('admin.categories.index', compact('categories'));
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
            'name' => 'required|string|max:100|unique:categories,name',
            // 'description' => 'nullable|string|max:500', // Nếu bạn quyết định dùng trường description
        ], [
            'name.required' => 'Tên danh mục không được để trống.',
            'name.unique' => 'Tên danh mục này đã tồn tại.',
            'name.max' => 'Tên danh mục không được vượt quá 100 ký tự.',
        ]);

        try {
            Category::create($request->only('name')); // Thêm 'description' nếu có
            return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công!');
        } catch (\Exception $e) {
            Log::error("Error creating category: " . $e->getMessage());
            return redirect()->route('admin.categories.index')->with('error', 'Có lỗi xảy ra khi thêm danh mục.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * (Dùng để lấy dữ liệu cho modal, không trả về view riêng)
     */
    public function edit(Category $category)
    {
        // Phương thức này có thể không cần thiết nếu bạn chỉ cập nhật qua modal và dữ liệu đã được truyền qua data attributes.
        // Tuy nhiên, nếu cần lấy dữ liệu phức tạp hơn hoặc trả về JSON cho AJAX thì sẽ hữu ích.
        // Hiện tại, view đã xử lý việc lấy data-name từ button, nên phương thức này có thể để trống hoặc xóa.
        // Hoặc có thể dùng để trả về JSON nếu muốn load bằng AJAX:
        // return response()->json($category);
        return redirect()->route('admin.categories.index'); // Hoặc đơn giản là redirect nếu không dùng
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id, // Bỏ qua chính nó khi check unique
            // 'description' => 'nullable|string|max:500', // Nếu có
        ], [
            'name.required' => 'Tên danh mục không được để trống.',
            'name.unique' => 'Tên danh mục này đã tồn tại.',
            'name.max' => 'Tên danh mục không được vượt quá 100 ký tự.',
        ]);

        try {
            $category->update($request->only('name')); // Thêm 'description' nếu có
            return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công!');
        } catch (\Exception $e) {
            Log::error("Error updating category ID {$category->id}: " . $e->getMessage());
            return redirect()->route('admin.categories.index')->with('error', 'Có lỗi xảy ra khi cập nhật danh mục.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            // Kiểm tra xem có bài đăng nào thuộc danh mục này không trước khi xóa (tùy chọn)
            if ($category->items()->count() > 0) {
                return redirect()->route('admin.categories.index')->with('error', 'Không thể xóa danh mục này vì vẫn còn bài đăng thuộc về nó.');
            }
            $categoryName = $category->name;
            $category->delete();
            return redirect()->route('admin.categories.index')->with('success', "Xóa danh mục '{$categoryName}' thành công!");
        } catch (\Exception $e) {
            Log::error("Error deleting category ID {$category->id}: " . $e->getMessage());
            return redirect()->route('admin.categories.index')->with('error', 'Có lỗi xảy ra khi xóa danh mục.');
        }
    }
}
