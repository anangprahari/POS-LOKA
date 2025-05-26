<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get total customers count
        $total_customers = Customer::count();

        // Get customers created today
        $customers_today = Customer::whereDate('created_at', today())->count();

        // Get total users count
        $total_users = User::count();

        // Get users created today
        $users_today = User::whereDate('created_at', today())->count();

        // Get total orders count
        $total_orders = Order::count();

        // Get orders created today
        $orders_today = Order::whereDate('created_at', today())->count();

        // Get customer activity data for the last 7 days
        $customer_activity = $this->getCustomerActivityData();

        // Get user activity data for the last 7 days
        $user_activity = $this->getUserActivityData();

        // Get order activity data for the last 7 days
        $order_activity = $this->getOrderActivityData();

        return view('user.dashboard', [
            'total_customers' => $total_customers,
            'customers_today' => $customers_today,
            'total_users' => $total_users,
            'users_today' => $users_today,
            'total_orders' => $total_orders,
            'orders_today' => $orders_today,
            'customer_activity_data' => $customer_activity['data'],
            'customer_activity_labels' => $customer_activity['labels'],
            'user_activity_data' => $user_activity['data'],
            'user_activity_labels' => $user_activity['labels'],
            'order_activity_data' => $order_activity['data'],
            'order_activity_labels' => $order_activity['labels']
        ]);
    }

    /**
     * Get customer activity data for the last 7 days
     * 
     * @return array
     */
    private function getCustomerActivityData()
    {
        $days = [];
        $data = [];
        $labels = [];

        // Generate last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('Y-m-d');
            $labels[] = $date->format('D'); // Mon, Tue, Wed, etc.
        }

        // Get customer count for each day
        $customerCounts = Customer::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->whereIn(DB::raw('DATE(created_at)'), $days)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('count', 'date')
            ->toArray();

        // Fill data array with counts (0 if no customers for that day)
        foreach ($days as $day) {
            $data[] = $customerCounts[$day] ?? 0;
        }

        return [
            'data' => $data,
            'labels' => $labels
        ];
    }

    /**
     * Get user activity data for the last 7 days
     * 
     * @return array
     */
    private function getUserActivityData()
    {
        $days = [];
        $data = [];
        $labels = [];

        // Generate last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('Y-m-d');
            $labels[] = $date->format('D');
        }

        // Get user count for each day
        $userCounts = User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->whereIn(DB::raw('DATE(created_at)'), $days)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('count', 'date')
            ->toArray();

        // Fill data array with counts (0 if no users for that day)
        foreach ($days as $day) {
            $data[] = $userCounts[$day] ?? 0;
        }

        return [
            'data' => $data,
            'labels' => $labels
        ];
    }

    /**
     * Get order activity data for the last 7 days
     * 
     * @return array
     */
    private function getOrderActivityData()
    {
        $days = [];
        $data = [];
        $labels = [];

        // Generate last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('Y-m-d');
            $labels[] = $date->format('D');
        }

        // Get order count for each day
        $orderCounts = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->whereIn(DB::raw('DATE(created_at)'), $days)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('count', 'date')
            ->toArray();

        // Fill data array with counts (0 if no orders for that day)
        foreach ($days as $day) {
            $data[] = $orderCounts[$day] ?? 0;
        }

        return [
            'data' => $data,
            'labels' => $labels
        ];
    }
}
