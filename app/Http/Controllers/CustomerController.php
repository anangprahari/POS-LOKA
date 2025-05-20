<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerStoreRequest;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel; // Added for export functionality
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Style\Color;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->wantsJson()) {
            return response(
                Customer::all()
            );
        }

        $customers = Customer::latest()->paginate(10);

        // Calculate customer statistics for dashboard
        $stats = [
            'total' => Customer::count(),
            'new' => Customer::where('created_at', '>=', now()->subDays(30))->count(),
            'returning' => $this->getReturningCustomersCount(),
            'vip' => $this->getVIPCustomersCount()
        ];

        return view('customers.index', compact('customers', 'stats'));
    }

    /**
     * Export customers to Excel file with elegant modern styling
     * 
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export()
    {
        $customers = Customer::with('orders')->get();

        // Define the column headers as key => value pairs
        $headers = [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'full_name' => 'Full Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'address' => 'Address',
            'status' => 'Status',
            'total_spent' => 'Total Spent',
            'orders' => 'Orders',
            'last_order' => 'Last Order',
            'joined_date' => 'Joined Date'
        ];

        // Format the data
        $formattedCustomers = $customers->map(function ($customer) use ($headers) {
            $totalSpent = $customer->orders->sum('total_amount');
            $orderCount = $customer->orders->count();
            $lastOrder = $customer->orders->sortByDesc('created_at')->first();

            return [
                $headers['id'] => $customer->id,
                $headers['first_name'] => $customer->first_name,
                $headers['last_name'] => $customer->last_name,
                $headers['full_name'] => $customer->first_name . ' ' . $customer->last_name,
                $headers['email'] => $customer->email,
                $headers['phone'] => $customer->phone,
                $headers['address'] => $customer->address,
                $headers['status'] => $customer->isVipCustomer() ? 'VIP' : ($customer->isReturningCustomer() ? 'Returning' : 'Regular'),
                $headers['total_spent'] => $totalSpent ? 'Rp. ' . number_format($totalSpent, 0, ',', '.') : 'Rp. 0',
                $headers['orders'] => $orderCount,
                $headers['last_order'] => $lastOrder ? $lastOrder->created_at->format('d M Y') : '-',
                $headers['joined_date'] => $customer->created_at->format('d M Y'),
            ];
        });

        // Modern header style
        $headerStyle = (new Style())
            ->setFontBold()
            ->setFontSize(13)
            ->setFontColor(Color::WHITE)
            ->setBackgroundColor('728FCE'); // Dark blue-gray color

        return (new FastExcel($formattedCustomers))
            ->headerStyle($headerStyle)
            ->download('customers_report.xlsx');
    }

    /**
     * Get returning customers count based on multiple purchases
     * 
     * @return int
     */
    private function getReturningCustomersCount()
    {
        return DB::table('customers')
            ->join('orders', 'customers.id', '=', 'orders.customer_id')
            ->select('customers.id')
            ->groupBy('customers.id')
            ->havingRaw('COUNT(orders.id) > 1')
            ->count();
    }

    /**
     * Get VIP customers count based on specific criteria
     * Total purchases exceeding $100,000 or more than 5 purchases
     * 
     * @return int
     */
    private function getVIPCustomersCount()
    {
        return DB::table('customers')
            ->join('orders', 'customers.id', '=', 'orders.customer_id')
            ->select('customers.id')
            ->groupBy('customers.id')
            ->havingRaw('SUM(orders.total_amount) > 100000 OR COUNT(orders.id) > 5')
            ->count();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerStoreRequest $request)
    {
        $avatar_path = '';

        if ($request->hasFile('avatar')) {
            $avatar_path = $request->file('avatar')->store('customers', 'public');
        }

        $customer = Customer::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'avatar' => $avatar_path,
            'user_id' => $request->user()->id,
        ]);

        if (!$customer) {
            return redirect()->back()->with('error', __('customer.error_creating'));
        }
        return redirect()->route('customers.index')->with('success', __('customer.succes_creating'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        // Load customer's orders
        $customer->load('orders');

        // Calculate customer stats
        $stats = [
            'total_spent' => $customer->orders->sum('total_amount'),
            'order_count' => $customer->orders->count(),
            'last_order' => $customer->getLastOrderDate(),
            'is_vip' => $customer->isVipCustomer(),
            'is_returning' => $customer->isReturningCustomer()
        ];

        return response()->json([
            'customer' => $customer,
            'stats' => $stats
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->address = $request->address;

        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($customer->avatar) {
                Storage::delete($customer->avatar);
            }
            // Store avatar
            $avatar_path = $request->file('avatar')->store('customers', 'public');
            // Save to Database
            $customer->avatar = $avatar_path;
        }

        if (!$customer->save()) {
            return redirect()->back()->with('error', __('customer.error_updating'));
        }
        return redirect()->route('customers.index')->with('success', __('customer.success_updating'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        if ($customer->avatar) {
            Storage::delete($customer->avatar);
        }

        $customer->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
