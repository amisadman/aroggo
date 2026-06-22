<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PrescriptionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $search = request('search');

        $query = Prescription::with('user')->orderBy('id', 'desc');

        if ($user->role === 'customer') {
            $query->where('user_id', $user->id);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('doctor_name', 'like', "%{$search}%")
                  ->orWhere('instructions', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $prescriptions = $query->paginate(6)->withQueryString();

        return Inertia::render('Prescription/Index', [
            'prescriptions' => $prescriptions,
            'filters' => ['search' => $search]
        ]);
    }

    public function create()
    {
        if (auth()->user()->role !== 'customer') {
            abort(403, 'Only customers can upload prescriptions.');
        }

        return Inertia::render('Prescription/Create');
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'customer') {
            abort(403, 'Only customers can upload prescriptions.');
        }

        $request->validate([
            'doctor_name' => 'required|string|max:255',
            'instructions' => 'nullable|string',
            'prescription_file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $filePath = null;
        if ($request->hasFile('prescription_file')) {
            $filePath = $request->file('prescription_file')->store('prescriptions', 'public');
        }

        Prescription::create([
            'user_id' => auth()->id(),
            'doctor_name' => $request->doctor_name,
            'instructions' => $request->instructions,
            'file_path' => $filePath,
            'status' => 'pending',
        ]);

        return redirect()->route('prescription.index')->with('message', 'Prescription uploaded successfully!');
    }

    public function update(Request $request, Prescription $prescription)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $user->role !== 'data_entry') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|string|in:pending,approved',
        ]);

        $prescription->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('message', 'Prescription status updated successfully!');
    }

    public function destroy(Prescription $prescription)
    {
        $user = auth()->user();
        // Only Admin can delete prescriptions, or customers can delete their pending prescriptions
        if ($user->role !== 'admin' && ($user->role !== 'customer' || $prescription->user_id !== $user->id)) {
            abort(403, 'Unauthorized action.');
        }

        if ($prescription->file_path) {
            Storage::disk('public')->delete($prescription->file_path);
        }

        $prescription->delete();

        return redirect()->back()->with('message', 'Prescription deleted successfully!');
    }
}
