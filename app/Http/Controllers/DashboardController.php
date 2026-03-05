<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\category;
use App\Models\ItemUnit;
use App\Models\SumberDana;
use App\Models\Loan;
use Carbon\Carbon;
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
        // Greeting
        $hour = Carbon::now()->hour;
        if ($hour < 12) {
            $greeting = 'Selamat pagi';
        } elseif ($hour < 17) {
            $greeting = 'Selamat siang';
        } elseif ($hour < 20) {
            $greeting = 'Selamat sore';
        } else {
            $greeting = 'Selamat malam';
        }

        // Statistics
        $totalItems = Item::count();
        $totalUnits = ItemUnit::count();
        $totalCategories = category::count();
        $totalFundingSources = SumberDana::count();

        // Available & Borrowed Units
        $availableUnits = ItemUnit::where('status', 'tersedia')->count();
        $borrowedUnits = ItemUnit::where('status', 'dipinjam')->count();

        // Low stock items
        $lowStockItems = Item::withCount('itemUnits')
            ->having('item_units_count', '<', 3)
            ->orderBy('item_units_count')
            ->limit(5)
            ->get();

        // Recent items
        $recentItems = Item::with('category')
            ->withCount('itemUnits')
            ->latest()
            ->limit(5)
            ->get();

        // Recent units
        $recentUnits = ItemUnit::with('item')
            ->latest()
            ->limit(5)
            ->get();

        // Items by category
        $itemsByCategory = category::withCount('items')
            ->having('items_count', '>', 0)
            ->orderBy('items_count', 'desc')
            ->limit(5)
            ->get();

        // Units by condition
        $unitsByCondition = ItemUnit::select(
                'condition',
                DB::raw('count(*) as total')
            )
            ->groupBy('condition')
            ->get();

        // Units by status
        $unitsByStatus = ItemUnit::select(
                'status',
                DB::raw('count(*) as total')
            )
            ->groupBy('status')
            ->get();

        // Top items
        $topItems = Item::withCount('itemUnits')
            ->orderByDesc('item_units_count')
            ->limit(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'greeting',
            'totalItems',
            'totalUnits',
            'totalCategories',
            'totalFundingSources',
            'availableUnits',
            'borrowedUnits',
            'lowStockItems',
            'recentItems',
            'recentUnits',
            'itemsByCategory',
            'unitsByCondition',
            'unitsByStatus',
            'topItems'
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

            'pending' => Loan::where('user_id', $userId)
                ->where('status', 'pending')
                ->count(),

            'borrowed' => Loan::where('user_id', $userId)
                ->where('status', 'borrowed')
                ->count(),

            'returned' => Loan::where('user_id', $userId)
                ->where('status', 'returned')
                ->count(),

            'overdue' => Loan::where('user_id', $userId)
                ->where('status', 'borrowed')
                ->whereDate('return_date', '<', now())
                ->count(),

            'total' => Loan::where('user_id', $userId)
                ->count(),
        ];

        // Recent loans
        $recentLoans = Loan::with('details.itemUnit.item')
            ->where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get();

        return view('user.dashboard.index', compact(
            'stats',
            'recentLoans'
        ));
    }

}