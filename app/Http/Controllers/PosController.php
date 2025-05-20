<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    /**
     * Display the POS interface
     */
    public function index()
    {
        return view('cart.index');
    }

    /**
     * Get list of products for POS interface
     */
    public function getProducts(Request $request)
    {
        $products = new Product();

        // Apply search if provided
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $products = $products->where('name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('barcode', 'LIKE', "%{$searchTerm}%");
        }

        // Apply category filter if provided
        if ($request->has('category_id') && $request->category_id) {
            $products = $products->where('category_id', $request->category_id);
        }

        // Filter by stock availability if required
        if ($request->has('in_stock') && $request->in_stock) {
            $products = $products->where('quantity', '>', 0);
        }

        // Get products with pagination
        $products = $products->latest()->paginate(16);

        // Return product resources for consistent API response
        return ProductResource::collection($products);
    }

    /**
     * Get recent transactions for POS interface
     */
    public function getRecentTransactions()
    {
        $orders = Order::with(['customer', 'payments', 'items.product'])
            ->latest()
            ->take(10) // Ambil 10 transaksi terbaru
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'invoice_number' => $order->id,
                    'created_at' => $order->created_at,
                    'customer' => $order->customer,
                    'payment_method' => $order->payment_method ?? $order->payments->last()?->payment_method ?? 'cash',
                    'note' => $order->note ?? '',
                    'subtotal' => $order->subtotal(),
                    'discount' => $order->discount ?? 0,
                    'discount_type' => $order->discount_type ?? 'fixed',
                    'discount_amount' => $order->discountAmount(),
                    'total' => $order->total(),
                    'paid' => $order->receivedAmount(),
                    'change' => $order->change(),
                    'is_paid' => $order->isPaid(),
                    'remaining' => $order->remainingAmount(),
                    'order_items' => $order->items->map(function ($item) {
                        return [
                            'product' => $item->product,
                            'quantity' => $item->quantity,
                            'price' => $item->price / $item->quantity, // Harga per item
                            'total' => $item->price, // Total harga item × quantity
                        ];
                    }),
                ];
            });

        return response()->json($orders);
    }
}
