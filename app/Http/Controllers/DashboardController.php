<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\ItemUnit;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | ADMIN DASHBOARD
    |--------------------------------------------------------------------------
    */ 
    public function dashboard()
    {
        if (auth()->user()->role == 'admin') {
            return $this->adminDashboard();
        }

        return $this->userDashboard();
    }

    public function adminDashboard()
    {
        // ==================== UNIT ====================
        $totalUnits = ItemUnit::count();
        $availableUnits = ItemUnit::where('status', 'tersedia')->count();
        $borrowedUnits = ItemUnit::where('status', 'dipinjam')->count();
        
        // ==================== KATEGORI ====================
        $totalCategories = Category::count();

        // ==================== PEMINJAMAN TERBARU ====================
        $recentLoans = Loan::with('user')
            ->latest()
            ->limit(5)
            ->get();

        // ==================== BARANG STOK RENDAH ====================
        $lowStockItems = Item::with('category')
            ->withCount(['itemUnits' => function($q) {
                $q->where('status', 'tersedia');
            }])
            ->having('item_units_count', '<', 3)
            ->orderBy('item_units_count')
            ->limit(5)
            ->get();

        // ==================== BARANG TERBARU ====================
        $recentItems = Item::with('category')
            ->latest()
            ->limit(5)
            ->get();

        // ==================== TOTAL PENGGUNA (Opsional) ====================
        $totalUsers = User::count();
        
        // ==================== TOTAL PEMINJAMAN (Opsional) ====================
        $totalLoans = Loan::count();

        return view('admin.dashboard.index', compact(
            'totalUnits',
            'availableUnits',
            'borrowedUnits',
            'totalCategories',
            'recentLoans',
            'lowStockItems',
            'recentItems',
            'totalUsers',
            'totalLoans'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | USER DASHBOARD
    |--------------------------------------------------------------------------
    */
    public function userDashboard()
{
    $userId = auth()->id();

    // Statistik user
    $stats = [
        'pending' => Loan::where('user_id', $userId)->where('status', 'pending')->count(),
        'approved' => Loan::where('user_id', $userId)->where('status', 'approved')->count(),
        'borrowed' => Loan::where('user_id', $userId)->where('status', 'borrowed')->count(),
        'returned' => Loan::where('user_id', $userId)->where('status', 'returned')->count(),
        'rejected' => Loan::where('user_id', $userId)->where('status', 'rejected')->count(),
        'overdue' => Loan::where('user_id', $userId)
            ->where('status', 'borrowed')
            ->whereDate('return_date', '<', now())
            ->count(),
        'total' => Loan::where('user_id', $userId)->count(),
    ];

    // Recent loans dengan join ke tabel lain
    $recentLoans = Loan::where('user_id', $userId)
        ->with(['details' => function($query) {
            $query->with('itemUnit.item');
        }])
        ->latest()
        ->limit(5)
        ->get();

    return view('user.dashboard.index', compact('stats', 'recentLoans'));
}
}