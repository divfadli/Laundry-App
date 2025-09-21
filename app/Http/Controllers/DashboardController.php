<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Customers;
use App\Models\TypeOfServices;
use App\Models\TransOrders;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $data = $this->getCommonData();

        if ($user->isAdmin()) {
            return view('dashboard.admin', compact('data'));
        } elseif ($user->isOperator()) {
            return view('dashboard.operator', compact('data'));
        } elseif ($user->isPimpinan()) {
            // Tambahkan daily_summary untuk pimpinan
            $data['daily_summary'] = TransOrders::selectRaw(
                'DATE(order_date) as date, 
                COUNT(*) as total_orders, 
                SUM(CASE WHEN order_status=1 THEN 1 ELSE 0 END) as completed_orders, 
                SUM(CASE WHEN order_status=0 THEN 1 ELSE 0 END) as pending_orders, 
                SUM(CASE WHEN order_status=1 THEN total ELSE 0 END) as revenue'
            )
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(7)
                ->get()
                ->toArray();

            return view('dashboard.pimpinan', compact('data'));
        }

        abort(403, 'Unauthorized');
    }

    // Fungsi helper untuk data umum
    private function getCommonData()
    {
        return [
            'total_customers' => Customers::count(),
            'total_services' => TypeOfServices::count(),
            'total_orders' => TransOrders::count(),
            'total_users' => User::count(),
            'orders_today' => TransOrders::whereDate('order_date', Carbon::today())->count(),
            'pending_orders' => TransOrders::where('order_status', 0)->count(),
            'completed_orders' => TransOrders::where('order_status', 1)->count(),
            'today_revenue' => TransOrders::whereDate('order_date', Carbon::today())->where('order_status', 1)->sum('total'),
            'recent_orders' => TransOrders::with(['customer' => fn($q) => $q->withTrashed()])
                ->latest()
                ->limit(5)
                ->get()
        ];
    }
}