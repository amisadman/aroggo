<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Company;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $medicines = Medicine::with(['company', 'category', 'location'])
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhereHas('company', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('category', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('location', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
            })
            ->orderBy('id', 'desc')
            ->paginate(6)
            ->withQueryString();

        return Inertia::render('Medicine/Index', [
            'medicines' => $medicines,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        $companies = Company::where('status', true)->get(['id', 'name']);
        $categories = Category::where('status', true)->get(['id', 'name']);
        $locations = Location::where('status', true)->get(['id', 'name']);
        
        return Inertia::render('Medicine/Create', compact('companies', 'categories', 'locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3',
            'company_id' => 'required|exists:companies,id',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'pack_details' => 'required',
            'quantity' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
        ]);

        Medicine::create([
            'name' => $request->name,
            'company_id' => $request->company_id,
            'category_id' => $request->category_id,
            'location_id' => $request->location_id,
            'pack_details' => $request->pack_details,
            'quantity' => $request->quantity,
            'price' => $request->price ?? 0.00,
            'status' => $request->status ?? true,
        ]);

        return redirect()->route('medicine.index')->with('message', 'Medicine Created Successfully!!');
    }

    public function edit(Medicine $medicine)
    {
        $companies = Company::where('status', true)->get(['id', 'name']);
        $categories = Category::where('status', true)->get(['id', 'name']);
        $locations = Location::where('status', true)->get(['id', 'name']);
        
        return Inertia::render('Medicine/Edit', compact('medicine', 'companies', 'categories', 'locations'));
    }

    public function update(Request $request, Medicine $medicine)
    {
        $request->validate([
            'name' => 'required|min:3',
            'company_id' => 'required|exists:companies,id',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'pack_details' => 'required',
            'quantity' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
        ]);

        $medicine->update([
            'name' => $request->name,
            'company_id' => $request->company_id,
            'category_id' => $request->category_id,
            'location_id' => $request->location_id,
            'pack_details' => $request->pack_details,
            'quantity' => $request->quantity,
            'price' => $request->price ?? 0.00,
            'status' => $request->status,
        ]);

        return redirect()->route('medicine.index')->with('message', 'Medicine Updated Successfully!!');
    }

    public function updateStock(Request $request, Medicine $medicine)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $medicine->update([
            'quantity' => $request->quantity,
        ]);

        return redirect()->back()->with('message', 'Stock Updated Successfully!!');
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();

        $fileHandle = fopen($filePath, 'r');
        $header = fgetcsv($fileHandle); // Read headers

        if ($header === false) {
            return redirect()->back()->with('error', 'Invalid CSV File!');
        }

        // Map header index
        $headerMap = array_flip(array_map('trim', $header));

        while (($row = fgetcsv($fileHandle)) !== false) {
            // Trim values
            $row = array_map('trim', $row);

            $name = $row[$headerMap['name'] ?? 0] ?? null;
            $companyName = $row[$headerMap['company'] ?? 1] ?? null;
            $categoryName = $row[$headerMap['category'] ?? 2] ?? null;
            $locationName = $row[$headerMap['location'] ?? 3] ?? null;
            $quantity = intval($row[$headerMap['quantity'] ?? 4] ?? 0);
            $price = floatval($row[$headerMap['price'] ?? $headerMap['unit_price'] ?? -1] ?? 0.00);
            $packDetails = $row[$headerMap['pack_details'] ?? 5] ?? '';

            if (!$name) continue;

            // Find or create Company
            $companyId = null;
            if ($companyName) {
                $company = Company::firstOrCreate(
                    ['name' => $companyName],
                    ['short_name' => strtoupper(substr($companyName, 0, 3)), 'status' => true]
                );
                $companyId = $company->id;
            }

            // Find or create Category
            $categoryId = null;
            if ($categoryName) {
                $category = Category::firstOrCreate(
                    ['name' => $categoryName],
                    ['status' => true]
                );
                $categoryId = $category->id;
            }

            // Find or create Location
            $locationId = null;
            if ($locationName) {
                $location = Location::firstOrCreate(
                    ['name' => $locationName],
                    ['status' => true]
                );
                $locationId = $location->id;
            }

            // Find or create Medicine
            Medicine::updateOrCreate(
                ['name' => $name, 'company_id' => $companyId],
                [
                    'category_id' => $categoryId,
                    'location_id' => $locationId,
                    'quantity' => $quantity,
                    'price' => $price,
                    'pack_details' => $packDetails,
                    'status' => true
                ]
            );
        }

        fclose($fileHandle);

        return redirect()->back()->with('message', 'CSV Medicines Imported Successfully!!');
    }

    public function destroy(Medicine $medicine)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->back()->with('message', 'Unauthorized: Only admins can delete records!');
        }

        $medicine->delete();

        return redirect()->back()->with('message', 'Medicine deleted Successfully!!');
    }
}
