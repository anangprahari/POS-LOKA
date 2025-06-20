<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Rap2hpoutre\FastExcel\FastExcel;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Style\Color;


class ProductController extends Controller
{
    /**
     * Display a listing of products.
     * This method can be accessed by both admin and user roles.
     */
    public function index(Request $request)
    {
        $products = new Product();

        // Apply search filter if search parameter exists
        if ($request->search) {
            $products = $products->where('name', 'LIKE', "%{$request->search}%");
        }

        // Apply category filter if category_id parameter exists
        if ($request->has('category_id') && $request->category_id) {
            $products = $products->where('category_id', $request->category_id);
        }

        // Get products with pagination
        $products = $products->latest()->paginate(10);

        // Check if request is for API or web view
        if ($request->wantsJson()) {
            return ProductResource::collection($products);
        }

        return view('products.index')->with('products', $products);
    }

    /**
     * Export products to Excel file
     * 
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export()
    {
        $products = Product::all();

        $formattedProducts = $products->map(function ($product) {
            return [
                'ID' => $product->id,
                'Name' => $product->name,
                'Barcode' => $product->barcode,
                'Price' => 'Rp. ' . number_format($product->price, 0, ',', '.'),
                'Quantity' => $product->quantity . ' cup',
                'Status' => $product->status ? 'Active' : 'Inactive',
                'Created At' => $product->created_at->format('Y-m-d H:i:s'),
                'Updated At' => $product->updated_at->format('Y-m-d H:i:s'),
            ];
        });

        // Create style for header
        $headerStyle = (new Style())
            ->setFontBold()
            ->setFontSize(13)
            ->setFontColor(Color::WHITE)
            ->setBackgroundColor('728FCE'); // Warna biru

        return (new FastExcel($formattedProducts))
            ->headerStyle($headerStyle)
            ->download('products.xlsx');
    }


    /**
     * Show the form for creating a new product.
     * Admin only method, already restricted in routes.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ProductStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductStoreRequest $request)
    {
        $image_path = '';

        if ($request->hasFile('image')) {
            $image_path = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $image_path,
            'barcode' => $request->barcode,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'status' => $request->status
        ]);

        if (!$product) {
            return redirect()->back()->with('error', __('product.error_creating'));
        }
        return redirect()->route('products.index')->with('success', __('product.success_creating'));
    }

    /**
     * Display the specified product.
     * This method can be accessed by both admin and user roles.
     * Fixed to handle both JSON and regular requests properly
     */
    public function show(Product $product, Request $request)
    {
        // Check if this is an AJAX request for JSON data
        if ($request->wantsJson() || $request->ajax()) {
            // Return JSON response with proper image URL
            $productData = [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'barcode' => $product->barcode,
                'price' => $product->price,
                'quantity' => $product->quantity,
                'status' => $product->status,
                'image_url' => $product->image ? Storage::url($product->image) : null,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];

            return response()->json([
                'success' => true,
                'data' => $productData
            ]);
        }

        // Regular web view
        return view('products.show')->with('product', $product);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return view('products.edit')->with('product', $product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ProductUpdateRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        $product->name = $request->name;
        $product->description = $request->description;
        $product->barcode = $request->barcode;
        $product->price = $request->price;
        $product->quantity = $request->quantity;
        $product->status = $request->status;

        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::delete($product->image);
            }
            // Store image
            $image_path = $request->file('image')->store('products', 'public');
            // Save to Database
            $product->image = $image_path;
        }

        if (!$product->save()) {
            return redirect()->back()->with('error', __('product.error_updating'));
        }
        return redirect()->route('products.index')->with('success', __('product.success_updating'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::delete($product->image);
        }
        $product->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
