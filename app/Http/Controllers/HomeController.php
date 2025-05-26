<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Rap2hpoutre\FastExcel\FastExcel;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Style\Color;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard for admin.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Pastikan user adalah admin
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('user.dashboard');
        }

        $orders = Order::with(['items', 'payments'])->get();
        $customers_count = Customer::count();

        $low_stock_products = Product::where('quantity', '<', 10)->get();

        $bestSellingProducts = DB::table('products')
            ->select(
                'products.id',
                'products.name',
                'products.description',
                'products.image',
                'products.barcode',
                'products.price',
                'products.quantity',
                'products.status',
                'products.created_at',
                'products.updated_at',
                DB::raw('SUM(order_items.quantity) AS total_sold')
            )
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->groupBy(
                'products.id',
                'products.name',
                'products.description',
                'products.image',
                'products.barcode',
                'products.price',
                'products.quantity',
                'products.status',
                'products.created_at',
                'products.updated_at'
            )
            ->havingRaw('SUM(order_items.quantity) > 10') // atau sesuaikan
            ->get();

        $currentMonthBestSelling = DB::table('products')
            ->select(
                'products.id',
                'products.name',
                'products.description',
                'products.image',
                'products.barcode',
                'products.price',
                'products.quantity',
                'products.status',
                'products.created_at',
                'products.updated_at',
                DB::raw('SUM(order_items.quantity) AS total_sold')
            )
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereYear('orders.created_at', date('Y'))
            ->whereMonth('orders.created_at', date('m'))
            ->groupBy(
                'products.id',
                'products.name',
                'products.description',
                'products.image',
                'products.barcode',
                'products.price',
                'products.quantity',
                'products.status',
                'products.created_at',
                'products.updated_at'
            )
            ->havingRaw('SUM(order_items.quantity) > 50') // Best-selling threshold for the current month
            ->get();

        // Hot products in the past six months
        $pastSixMonthsHotProducts = DB::table('products')
            ->select(
                'products.id',
                'products.name',
                'products.description',
                'products.image',
                'products.barcode',
                'products.price',
                'products.quantity',
                'products.status',
                'products.created_at',
                'products.updated_at',
                DB::raw('SUM(order_items.quantity) AS total_sold')
            )
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.created_at', '>=', now()->subMonths(6)) // Filter for the past 6 months
            ->groupBy(
                'products.id',
                'products.name',
                'products.description',
                'products.image',
                'products.barcode',
                'products.price',
                'products.quantity',
                'products.status',
                'products.created_at',
                'products.updated_at'
            )
            ->havingRaw('SUM(order_items.quantity) > 1000') // Hot product threshold for past 6 months
            ->get();

        // Get last login time - Handle if last_login_at is not available in the database
        $lastLogin = null;

        // Check if the last_login_at column exists in the users table before trying to access it
        if (Schema::hasColumn('users', 'last_login_at')) {
            $lastLogin = auth()->user()->last_login_at ?? auth()->user()->created_at;
        } else {
            // Fallback to created_at if last_login_at column doesn't exist
            $lastLogin = auth()->user()->created_at;
        }

        // Get orders since last login
        $newOrdersCount = Order::where('created_at', '>', $lastLogin)->count();

        // Calculate monthly target progress
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Get total income for current month
        $monthlyIncome = Order::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->get()
            ->map(function ($order) {
                return $order->receivedAmount() > $order->total() ? $order->total() : $order->receivedAmount();
            })
            ->sum();

        // Assume monthly target from settings or a fixed value for now
        $monthlyTarget = config('settings.monthly_target', 40000000); // Default to 10000 if not set
        $targetProgress = min(($monthlyIncome / $monthlyTarget) * 100, 100); // Cap at 100%

        // Get last 7 days income data
        $last7DaysData = [];
        $last7DaysLabels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $last7DaysLabels[] = $date->format('D'); // Day name (Mon, Tue, etc.)

            $dayIncome = Order::whereDate('created_at', $date->format('Y-m-d'))
                ->get()
                ->map(function ($order) {
                    return $order->receivedAmount() > $order->total() ? $order->total() : $order->receivedAmount();
                })
                ->sum();

            $last7DaysData[] = $dayIncome;
        }

        // Update user's last login time - Check if column exists before updating
        try {
            if (Schema::hasColumn('users', 'last_login_at')) {
                DB::table('users')
                    ->where('id', auth()->id())
                    ->update(['last_login_at' => now()]);
            }
        } catch (\Exception $e) {
            // Log error or handle silently
            Log::error('Error updating last_login_at: ' . $e->getMessage());
        }

        return view('home', [
            'orders_count' => $orders->count(),
            'income' => $orders->map(function ($i) {
                return $i->receivedAmount() > $i->total() ? $i->total() : $i->receivedAmount();
            })->sum(),
            'income_today' => $orders->where('created_at', '>=', date('Y-m-d') . ' 00:00:00')->map(function ($i) {
                return $i->receivedAmount() > $i->total() ? $i->total() : $i->receivedAmount();
            })->sum(),
            'customers_count' => $customers_count,
            'low_stock_products' => $low_stock_products,
            'best_selling_products' => $bestSellingProducts,
            'current_month_products' => $currentMonthBestSelling,
            'past_months_products' => $pastSixMonthsHotProducts,
            'new_orders_count' => $newOrdersCount,
            'last_login_at' => $lastLogin,
            'target_progress' => $targetProgress,
            'monthly_target' => $monthlyTarget,
            'last_7_days_data' => json_encode($last7DaysData),
            'last_7_days_labels' => json_encode($last7DaysLabels),
        ]);
    }

    /**
     * Show dashboard for regular users.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function userDashboard()
    {
        // Data sederhana untuk user dashboard
        $customers_count = Customer::count();

        // Hanya tampilkan recent orders jika diperlukan
        $recent_orders = Order::with(['items'])
            ->latest()
            ->take(5)
            ->get();

        return view('user.dashboard', [
            'customers_count' => $customers_count,
            'recent_orders' => $recent_orders
        ]);
    }

    /**
     * Create header style for Excel export
     *
     * @return Style
     */
    private function getHeaderStyle()
    {
        return (new Style())
            ->setFontBold()
            ->setFontSize(13)
            ->setFontColor(Color::WHITE)
            ->setBackgroundColor('728FCE'); // Warna biru
    }

    /**
     * Format product data for Excel export
     *
     * @param mixed $products
     * @return \Illuminate\Support\Collection
     */
    private function formatProductsForExport($products)
    {
        return collect($products)->map(function ($product) {
            $currencySymbol = config('settings.currency_symbol', 'Rp. ');

            return [
                'ID' => $product->id,
                'Name' => $product->name,
                'Barcode' => $product->barcode,
                'Price' => $currencySymbol . ' ' . number_format($product->price, 2),
                'Quantity' => $product->quantity,
                'Status' => $product->status ? __('common.Active') : __('common.Inactive'),
                'Updated At' => $product->updated_at instanceof \DateTime
                    ? $product->updated_at->format('Y-m-d H:i:s')
                    : $product->updated_at,
            ];
        });
    }

    /**
     * Export low stock products to Excel
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportLowStockProducts()
    {
        $products = Product::where('quantity', '<', 10)->get();
        $formattedProducts = $this->formatProductsForExport($products);

        return (new FastExcel($formattedProducts))
            ->headerStyle($this->getHeaderStyle())
            ->download('low_stock_products.xlsx');
    }

    /**
     * Export hot products (current month) to Excel
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportHotProducts()
    {
        $products = DB::table('products')
            ->select(
                'products.id',
                'products.name',
                'products.description',
                'products.image',
                'products.barcode',
                'products.price',
                'products.quantity',
                'products.status',
                'products.created_at',
                'products.updated_at',
                DB::raw('SUM(order_items.quantity) AS total_sold')
            )
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereYear('orders.created_at', date('Y'))
            ->whereMonth('orders.created_at', date('m'))
            ->groupBy(
                'products.id',
                'products.name',
                'products.description',
                'products.image',
                'products.barcode',
                'products.price',
                'products.quantity',
                'products.status',
                'products.created_at',
                'products.updated_at'
            )
            ->havingRaw('SUM(order_items.quantity) > 50')
            ->get();

        $formattedProducts = $this->formatProductsForExport($products);

        // Add total sold column for hot products
        $formattedProducts = $formattedProducts->map(function ($item, $key) use ($products) {
            $item['Total Sold'] = $products[$key]->total_sold;
            return $item;
        });

        return (new FastExcel($formattedProducts))
            ->headerStyle($this->getHeaderStyle())
            ->download('hot_products_current_month.xlsx');
    }

    /**
     * Export hot products (past months) to Excel
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportHotProductsYear()
    {
        $products = DB::table('products')
            ->select(
                'products.id',
                'products.name',
                'products.description',
                'products.image',
                'products.barcode',
                'products.price',
                'products.quantity',
                'products.status',
                'products.created_at',
                'products.updated_at',
                DB::raw('SUM(order_items.quantity) AS total_sold')
            )
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.created_at', '>=', now()->subMonths(6))
            ->groupBy(
                'products.id',
                'products.name',
                'products.description',
                'products.image',
                'products.barcode',
                'products.price',
                'products.quantity',
                'products.status',
                'products.created_at',
                'products.updated_at'
            )
            ->havingRaw('SUM(order_items.quantity) > 1000')
            ->get();

        $formattedProducts = $this->formatProductsForExport($products);

        // Add total sold column for hot products
        $formattedProducts = $formattedProducts->map(function ($item, $key) use ($products) {
            $item['Total Sold'] = $products[$key]->total_sold;
            return $item;
        });

        return (new FastExcel($formattedProducts))
            ->headerStyle($this->getHeaderStyle())
            ->download('hot_products_past_six_months.xlsx');
    }

    /**
     * Export best selling products to Excel
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportBestSellingProducts()
    {
        $products = DB::table('products')
            ->select(
                'products.id',
                'products.name',
                'products.description',
                'products.image',
                'products.barcode',
                'products.price',
                'products.quantity',
                'products.status',
                'products.created_at',
                'products.updated_at',
                DB::raw('SUM(order_items.quantity) AS total_sold')
            )
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->groupBy(
                'products.id',
                'products.name',
                'products.description',
                'products.image',
                'products.barcode',
                'products.price',
                'products.quantity',
                'products.status',
                'products.created_at',
                'products.updated_at'
            )
            ->havingRaw('SUM(order_items.quantity) > 10')
            ->get();

        $formattedProducts = $this->formatProductsForExport($products);

        // Add total sold column for best selling products
        $formattedProducts = $formattedProducts->map(function ($item, $key) use ($products) {
            $item['Total Sold'] = $products[$key]->total_sold;
            return $item;
        });

        return (new FastExcel($formattedProducts))
            ->headerStyle($this->getHeaderStyle())
            ->download('best_selling_products.xlsx');
    }
}
