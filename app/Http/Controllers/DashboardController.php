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

    public function dashboard()
    {
        if (auth()->user()->role == 'admin') {
            return $this->adminDashboard();
        }

        return $this->userDashboard();
    }

    public function adminDashboard()
    {

        $totalUnits = ItemUnit::count();
        $availableUnits = ItemUnit::where('status', 'tersedia')->count();
        $borrowedUnits = ItemUnit::where('status', 'dipinjam')->count();

        $totalCategories = Category::count();

        $recentLoans = Loan::with('user')
            ->latest()
            ->limit(5)
            ->get();

        $lowStockItems = Item::with('category')
            ->withCount(['itemUnits' => function ($q) {
                $q->where('status', 'tersedia');
            }])
            ->having('item_units_count', '<', 3)
            ->orderBy('item_units_count')
            ->limit(5)
            ->get();

        $recentItems = Item::with('category')
            ->latest()
            ->limit(5)
            ->get();

        $totalUsers = User::count();

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

        $recentLoans = Loan::where('user_id', $userId)
            ->with(['details' => function ($query) {
                $query->with('itemUnit.item');
            }])
            ->latest()
            ->limit(5)
            ->get();

        return view('user.dashboard.index', compact('stats', 'recentLoans'));
    }
}
