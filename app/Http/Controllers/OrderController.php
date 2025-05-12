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
        $orders = new Order();
        if ($request->start_date) {
            $orders = $orders->where('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $orders = $orders->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }
        $orders = $orders->with(['items.product', 'payments', 'customer'])->latest()->paginate(10);

        $subtotalSum = $orders->sum(function ($order) {
            return $order->subtotal();
        });

        $discountSum = $orders->sum(function ($order) {
            return $order->discountAmount();
        });

        $total = $orders->sum(function ($order) {
            return $order->total();
        });

        $receivedAmount = $orders->sum(function ($order) {
            return $order->receivedAmount();
        });

        return view('orders.index', compact('orders', 'subtotalSum', 'discountSum', 'total', 'receivedAmount'));
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
