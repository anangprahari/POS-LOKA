<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Create query builder instance without pagination
        $ordersQuery = new Order();

        // Apply date filters if provided
        if ($request->start_date) {
            $ordersQuery = $ordersQuery->where('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $ordersQuery = $ordersQuery->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        // First calculate totals from all matching orders (without pagination)
        $allOrders = clone $ordersQuery;

        // Get total counts for the dashboard
        $totalOrders = $allOrders->count();

        // Use DB aggregate queries for better performance on large datasets
        $totals = $allOrders->selectRaw('
            COUNT(*) as total_count,
            SUM(CASE WHEN discount_type = "percentage" THEN 
                (SELECT SUM(items.price) FROM order_items as items WHERE items.order_id = orders.id) * discount / 100
                ELSE discount END) as total_discount
        ')->first();

        // Calculate total for all orders using relationships and raw queries
        $orderIds = $allOrders->pluck('id')->toArray();

        // Get subtotal using order items
        $subtotalSum = DB::table('order_items')
            ->whereIn('order_id', $orderIds)
            ->sum('price');

        // Get total discount amount
        $discountSum = $totals->total_discount ?? 0;

        // Calculate total amount (subtotal - discount)
        $total = $subtotalSum - $discountSum;

        // Get received amount
        $receivedAmount = DB::table('payments')
            ->whereIn('order_id', $orderIds)
            ->sum('amount');

        // Now get the paginated orders for displaying in the table
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
            ->take(10) // Ambil 10 transaksi terbaru
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
                    'order_items' => $order->items->map(function ($item) {
                        return [
                            'product' => $item->product,
                            'quantity' => $item->quantity,
                            'price' => $item->price / $item->quantity, // Harga per item
                            'total' => $item->price,
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
