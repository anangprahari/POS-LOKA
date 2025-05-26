<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Style\Color;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Gunakan query builder
        $ordersQuery = Order::query();

        // Apply date filters if provided
        if ($request->start_date) {
            $ordersQuery->where('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $ordersQuery->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        $allOrders = clone $ordersQuery;

        // Gunakan untuk menghitung total orders
        $totalOrders = $allOrders->count();

        // Gunakan query terpisah untuk kalkulasi discount
        $totals = (clone $ordersQuery)->selectRaw('
            COUNT(*) as total_count,
            SUM(CASE WHEN discount_type = "percentage" THEN 
                (SELECT SUM(items.price) FROM order_items as items WHERE items.order_id = orders.id) * discount / 100
                ELSE discount END) as total_discount
        ')->first();

        // Gunakan query standar (tanpa selectRaw) untuk pluck ID
        $orderIds = (clone $ordersQuery)->pluck('id')->toArray();


        $subtotalSum = DB::table('order_items')
            ->whereIn('order_id', $orderIds)
            ->sum('price');

        $discountSum = $totals->total_discount ?? 0;

        $total = $subtotalSum - $discountSum;

        $receivedAmount = DB::table('payments')
            ->whereIn('order_id', $orderIds)
            ->sum('amount');

        $orders = $ordersQuery->with(['items.product', 'payments', 'customer'])->latest()->paginate(10);

        return view('orders.index', compact(
            'orders',
            'subtotalSum',
            'discountSum',
            'total',
            'receivedAmount',
            'totalOrders'
        ));
    }


    /**
     * Export orders to Excel file with elegant modern styling
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        // Build the query with the same filters as the index method
        $ordersQuery = new Order();

        // Apply date filters if provided
        if ($request->start_date) {
            $ordersQuery = $ordersQuery->where('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $ordersQuery = $ordersQuery->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        // Get all orders with their relationships
        $orders = $ordersQuery->with(['items.product', 'payments', 'customer'])->latest()->get();

        // Define the column headers
        $headers = [
            'invoice' => 'Invoice #',
            'date' => 'Date',
            'customer' => 'Customer',
            'payment_method' => 'Payment Method',
            'subtotal' => 'Subtotal',
            'discount' => 'Discount',
            'total' => 'Total',
            'paid' => 'Paid Amount',
            'status' => 'Payment Status',
            'items_count' => 'Items Count',
            'note' => 'Note'
        ];

        // Format the data
        $formattedOrders = $orders->map(function ($order) use ($headers) {
            $subtotal = $order->subtotal();
            $discountAmount = $order->discountAmount();
            $total = $order->total();
            $paid = $order->receivedAmount();
            $itemsCount = $order->items->sum('quantity');

            // Get customer name, handling null case
            $customerName = $order->customer ?
                ($order->customer->first_name . ' ' . $order->customer->last_name) :
                'Walk-in Customer';

            return [
                $headers['invoice'] => 'INV-' . str_pad($order->id, 5, '0', STR_PAD_LEFT),
                $headers['date'] => $order->created_at->format('d M Y H:i'),
                $headers['customer'] => $customerName,
                $headers['payment_method'] => ucfirst($order->payment_method ?? $order->payments->last()?->payment_method ?? 'cash'),
                $headers['subtotal'] => 'Rp. ' . number_format($subtotal, 0, ',', '.'),
                $headers['discount'] => 'Rp. ' . number_format($discountAmount, 0, ',', '.'),
                $headers['total'] => 'Rp. ' . number_format($total, 0, ',', '.'),
                $headers['paid'] => 'Rp. ' . number_format($paid, 0, ',', '.'),
                $headers['items_count'] => $itemsCount,
                $headers['note'] => $order->note ?? '-'
            ];
        });

        // Create header style with modern look
        $headerStyle = (new Style())
            ->setFontBold()
            ->setFontSize(13)
            ->setFontColor(Color::WHITE)
            ->setBackgroundColor('728FCE'); // Dark blue-gray color

        // Generate and download the Excel file
        return (new FastExcel($formattedOrders))
            ->headerStyle($headerStyle)
            ->download('orders_report.xlsx');
    }

    public function store(OrderStoreRequest $request)
    {
        // Mulai transaksi database untuk memastikan konsistensi data
        return DB::transaction(function () use ($request) {
            // Buat order baru
            $order = Order::create([
                'customer_id' => $request->customer_id,
                'user_id' => $request->user()->id,
                'payment_method' => $request->payment_method ?? 'cash',
                'note' => $request->note,
                'discount' => $request->discount ?? 0,
                'discount_type' => $request->discount_type ?? 'fixed',
            ]);

            // Ambil keranjang pengguna saat ini
            $cart = $request->user()->cart()->get();

            // Tambahkan item dari keranjang ke order
            foreach ($cart as $item) {
                $order->items()->create([
                    'price' => $item->price * $item->pivot->quantity,
                    'quantity' => $item->pivot->quantity,
                    'product_id' => $item->id,
                ]);

                // Kurangi stok produk
                $item->quantity = $item->quantity - $item->pivot->quantity;
                $item->save();
            }

            // Kosongkan keranjang pengguna
            $request->user()->cart()->detach();

            // Catat pembayaran
            $order->payments()->create([
                'amount' => $request->amount,
                'user_id' => $request->user()->id,
                'payment_method' => $request->payment_method ?? 'cash',
            ]);

            // Kembalikan data order untuk cetak struk
            $orderData = $this->getOrderDataForReceipt($order->id);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'order' => $orderData
            ]);
        });
    }

    /**
     * Export detailed order items report
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportDetails(Request $request)
    {
        // Build the query with the same filters as the index method
        $ordersQuery = new Order();

        // Apply date filters if provided
        if ($request->start_date) {
            $ordersQuery = $ordersQuery->where('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $ordersQuery = $ordersQuery->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        // Get all orders with their relationships
        $orders = $ordersQuery->with(['items.product', 'customer'])->latest()->get();

        // Prepare data for export - one row per order item
        $orderItems = collect();

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $orderItems->push([
                    'Invoice #' => 'INV-' . str_pad($order->id, 5, '0', STR_PAD_LEFT),
                    'Date' => $order->created_at->format('d M Y H:i'),
                    'Customer' => $order->customer ?
                        ($order->customer->first_name . ' ' . $order->customer->last_name) :
                        'Walk-in Customer',
                    'Product ID' => $item->product->id,
                    'Product Name' => $item->product->name,
                    'Barcode' => $item->product->barcode ?? '-',
                    'Quantity' => $item->quantity,
                    'Unit Price' => 'Rp. ' . number_format($item->price / $item->quantity, 0, ',', '.'),
                    'Total Price' => 'Rp. ' . number_format($item->price, 0, ',', '.')
                ]);
            }
        }

        // Create header style with modern look
        $headerStyle = (new Style())
            ->setFontBold()
            ->setFontSize(13)
            ->setFontColor(Color::WHITE)
            ->setBackgroundColor('728FCE'); // Dark blue-gray color

        // Generate and download the Excel file
        return (new FastExcel($orderItems))
            ->headerStyle($headerStyle)
            ->download('order_items_details.xlsx');
    }

    // Helper function untuk mendapatkan data order untuk struk
    private function getOrderDataForReceipt($orderId)
    {
        $order = Order::with(['customer', 'payments', 'items.product'])
            ->findOrFail($orderId);

        $subtotal = $order->subtotal();
        $discountAmount = $order->discountAmount();
        $total = $order->total();
        $paid = $order->receivedAmount();
        $change = $order->change();

        return [
            'id' => $order->id,
            'invoice_number' => $order->id,
            'created_at' => $order->created_at,
            'customer' => $order->customer,
            'payment_method' => $order->payment_method ?? $order->payments->last()?->payment_method ?? 'cash',
            'note' => $order->note ?? '',
            'subtotal' => $subtotal,
            'discount' => $order->discount ?? 0,
            'discount_type' => $order->discount_type ?? 'fixed',
            'discount_amount' => $discountAmount,
            'total' => $total,
            'paid' => $paid,
            'change' => $change,
            'order_items' => $order->items->map(function ($item) {
                return [
                    'product' => $item->product,
                    'quantity' => $item->quantity,
                    'price' => $item->price / $item->quantity, // Harga per item
                    'total' => $item->price, // Total harga item × quantity
                ];
            }),
        ];
    }

    public function recent()
    {
        $orders = Order::with(['customer', 'payments', 'items.product'])
            ->latest()
            ->take(10) // Get 10 most recent transactions
            ->get()
            ->map(function ($order) {
                $subtotal = $order->subtotal();
                $discountAmount = $order->discountAmount();
                $total = $order->total();
                $paid = $order->receivedAmount();
                $change = $order->change();

                return [
                    'id' => $order->id,
                    'invoice_number' => $order->id,
                    'created_at' => $order->created_at,
                    'customer' => $order->customer,
                    'payment_method' => $order->payment_method ?? $order->payments->last()?->payment_method ?? 'cash',
                    'note' => $order->note ?? '',
                    'subtotal' => $subtotal,
                    'discount' => $order->discount ?? 0,
                    'discount_type' => $order->discount_type ?? 'fixed',
                    'discount_amount' => $discountAmount,
                    'total' => $total,
                    'paid' => $paid,
                    'change' => $change,
                    'is_paid' => $order->isPaid(), // Make sure this method exists in your Order model
                    'remaining' => $order->remainingAmount(), // Make sure this method exists in your Order model
                    'order_items' => $order->items->map(function ($item) {
                        return [
                            'product' => $item->product,
                            'quantity' => $item->quantity,
                            'price' => $item->price / $item->quantity, // Price per item
                            'total' => $item->price, // Total price (item × quantity)
                        ];
                    }),
                ];
            });

        return response()->json($orders);
    }

    // Tambahkan method untuk mendapatkan detail order tunggal
    public function show($id)
    {
        return response()->json($this->getOrderDataForReceipt($id));
    }

    public function partialPayment(Request $request)
    {
        // Validasi input
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $orderId = $request->order_id;
        $amount = $request->amount;

        // Find the order
        $order = Order::findOrFail($orderId);

        // Check if the amount exceeds the remaining balance
        $remainingAmount = $order->total() - $order->receivedAmount();
        if ($amount > $remainingAmount) {
            return redirect()->route('orders.index')
                ->with('error', 'Amount exceeds remaining balance of ' .
                    config('settings.currency_symbol') . number_format($remainingAmount, 2));
        }

        // Save the payment
        DB::transaction(function () use ($order, $amount, $request) {
            $order->payments()->create([
                'amount' => $amount,
                'user_id' => auth()->user()->id,
                'payment_method' => $request->payment_method ?? 'cash',
            ]);
        });

        return redirect()->route('orders.index')
            ->with('success', 'Partial payment of ' .
                config('settings.currency_symbol') . number_format($amount, 2) .
                ' made successfully.');
    }
}
