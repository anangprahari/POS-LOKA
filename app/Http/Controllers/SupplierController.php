<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierStoreRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Rap2hpoutre\FastExcel\FastExcel;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Style\Color;

class SupplierController extends Controller
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
                Supplier::all()
            );
        }
        $suppliers = Supplier::latest()->paginate(10);
        return view('suppliers.index')->with('suppliers', $suppliers);
    }

    /**
     * Export suppliers to Excel file
     * 
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export()
    {
        $suppliers = Supplier::all();

        $formattedSuppliers = $suppliers->map(function ($supplier) {
            return [
                'ID' => $supplier->id,
                'First Name' => $supplier->first_name,
                'Last Name' => $supplier->last_name,
                'Email' => $supplier->email,
                'Phone' => $supplier->phone,
                'Address' => $supplier->address,
                'Created At' => $supplier->created_at->format('Y-m-d H:i:s'),
                'Updated At' => $supplier->updated_at->format('Y-m-d H:i:s'),
            ];
        });

        // Create style for header
        $headerStyle = (new Style())
            ->setFontBold()
            ->setFontSize(13)
            ->setFontColor(Color::WHITE)
            ->setBackgroundColor('728FCE'); // Warna biru

        return (new FastExcel($formattedSuppliers))
            ->headerStyle($headerStyle)
            ->download('suppliers.xlsx');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $avatar_path = '';

        if ($request->hasFile('avatar')) {
            $avatar_path = $request->file('avatar')->store('suppliers', 'public');
        }

        $supplier = Supplier::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'avatar' => $avatar_path,
        ]);

        if (!$supplier) {
            return redirect()->back()->with('error', __('supplier.error_creating'));
        }
        return redirect()->route('suppliers.index')->with('success', __('supplier.success_creating'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {
        // Jika request adalah AJAX, kembalikan data JSON
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($supplier);
        }

        // Jika bukan AJAX, tampilkan view
        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier $supplier)
    {
        // Validate incoming request
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email,' . $supplier->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $supplier->first_name = $request->first_name;
        $supplier->last_name = $request->last_name;
        $supplier->email = $request->email;
        $supplier->phone = $request->phone;
        $supplier->address = $request->address;

        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($supplier->avatar) {
                Storage::disk('public')->delete($supplier->avatar);
            }
            // Store new avatar
            $avatar_path = $request->file('avatar')->store('suppliers', 'public');
            // Save to Database
            $supplier->avatar = $avatar_path;
        }

        if (!$supplier->save()) {
            return redirect()->back()->with('error', __('supplier.error_updating'));
        }
        return redirect()->route('suppliers.index')->with('success', __('supplier success updating'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supplier $supplier)
    {
        if ($supplier->avatar) {
            Storage::disk('public')->delete($supplier->avatar);
        }

        $supplier->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
