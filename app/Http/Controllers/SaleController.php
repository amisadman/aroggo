<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with(['medicine.company', 'customer'])->orderBy('id', 'desc')->paginate(6);
        return Inertia::render('Sale/Index', compact('sales'));
    }

    public function create()
    {
        $medicines = Medicine::with(['location', 'category'])->where('status', true)->where('quantity', '>', 0)->get();
        $customers = \App\Models\User::where('role', 'customer')->get(['id', 'name', 'email', 'next_purchase_discount']);
        return Inertia::render('Sale/Create', compact('medicines', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'customer_id' => 'nullable|exists:users,id',
            'quantity' => 'required|integer|min:1',
            'total_price' => 'required|numeric|min:0',
        ]);

        $medicine = Medicine::findOrFail($request->medicine_id);

        if ($medicine->quantity < $request->quantity) {
            return redirect()->back()->withErrors(['quantity' => 'Not enough stock available! Current stock: ' . $medicine->quantity]);
        }

        // Deduct from stock
        $medicine->decrement('quantity', $request->quantity);

        // Apply discount and reset customer's discount to 0
        if ($request->customer_id) {
            $customer = \App\Models\User::find($request->customer_id);
            if ($customer && $customer->next_purchase_discount > 0) {
                $customer->update(['next_purchase_discount' => 0]);
            }
        }

        // Record Sale
        Sale::create([
            'medicine_id' => $request->medicine_id,
            'customer_id' => $request->customer_id,
            'quantity' => $request->quantity,
            'total_price' => $request->total_price,
        ]);

        return redirect()->route('sale.index')->with('message', 'Sale Recorded Successfully!!');
    }

    public function destroy(Sale $sale)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->back()->with('message', 'Unauthorized: Only admins can delete sales records!');
        }

        // Refund stock
        if ($sale->medicine) {
            $sale->medicine->increment('quantity', $sale->quantity);
        }

        $sale->delete();

        return redirect()->back()->with('message', 'Sale Deleted and Stock Refunded Successfully!!');
    }
}
