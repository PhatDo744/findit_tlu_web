<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Item;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalItems = Item::count();
        $pendingItems = Item::where('status', 'pending_approval')->count();
        $approvedItems = Item::where('status', 'approved')->count();

        $stats = [
            'total_users' => $totalUsers,
            'total_items' => $totalItems,
            'pending_items' => $pendingItems,
            'approved_items' => $approvedItems,
        ];

        $recentItems = Item::with('user') // Eager load user relationship
                            ->orderBy('created_at', 'desc')
                            ->take(10)
                            ->get();

        $newUsers = User::orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'recent_items' => $recentItems,
            'new_users' => $newUsers
        ]);
    }
}
