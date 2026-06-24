<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    
    if ($user->role === 'customer') {
        return Inertia::render('Dashboard', [
            'metrics' => [
                'purchases_count' => \App\Models\Sale::where('customer_id', $user->id)->count(),
                'total_spent' => \App\Models\Sale::where('customer_id', $user->id)->sum('total_price'),
                'prescriptions_pending' => \App\Models\Prescription::where('user_id', $user->id)->where('status', 'pending')->count(),
                'prescriptions_approved' => \App\Models\Prescription::where('user_id', $user->id)->where('status', 'approved')->count(),
            ],
            'purchaseHistory' => \App\Models\Sale::with(['medicine.company', 'medicine.location'])
                ->where('customer_id', $user->id)
                ->orderBy('id', 'desc')
                ->get(),
            'prescriptions' => \App\Models\Prescription::where('user_id', $user->id)
                ->orderBy('id', 'desc')
                ->get(),
        ]);
    }

    $adminMetrics = [
        'categories' => \App\Models\Category::count(),
        'locations' => \App\Models\Location::count(),
        'companies' => \App\Models\Company::count(),
        'suppliers' => \App\Models\Supplier::count(),
        'medicines' => \App\Models\Medicine::count(),
        'sales' => \App\Models\Sale::count(),
        'sales_revenue' => \App\Models\Sale::sum('total_price') ?? 0,
    ];

    $customerLeaderboard = \App\Models\User::where('role', 'customer')
        ->withSum('sales as total_spent', 'total_price')
        ->orderByRaw('COALESCE(total_spent, 0) DESC')
        ->get(['id', 'name', 'email', 'next_purchase_discount']);

    $recentSales = \App\Models\Sale::with(['medicine.company', 'customer'])
        ->orderBy('id', 'desc')
        ->take(5)
        ->get();

    return Inertia::render('Dashboard', [
        'metrics' => $adminMetrics,
        'users' => \App\Models\User::all(['id', 'name', 'email', 'role']),
        'leaderboard' => $customerLeaderboard,
        'recentSales' => $recentSales,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/users/staff', [RegisteredUserController::class, 'storeStaff'])->name('users.store-staff');
    Route::delete('/users/{user}', [RegisteredUserController::class, 'destroy'])->name('users.destroy');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('category', CategoryController::class);
    Route::resource('location', LocationController::class);
    Route::resource('company', CompanyController::class);
    Route::resource('supplier', SupplierController::class);
    Route::resource('medicine', MedicineController::class);
    Route::post('/medicine/{medicine}/update-stock', [MedicineController::class, 'updateStock'])->name('medicine.update-stock');
    Route::post('/medicine/import-csv', [MedicineController::class, 'importCsv'])->name('medicine.import-csv');
    Route::resource('sale', SaleController::class);
    Route::resource('prescription', PrescriptionController::class);
    Route::post('/customer/{user}/assign-discount', [RegisteredUserController::class, 'assignDiscount'])->name('customer.assign-discount');
});

require __DIR__ . '/auth.php';

// Temporary helper route for shared hosting to run database migrations and seeders
Route::get('/run-migrations', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
        return '✨ Database migrated and seeded successfully!';
    } catch (\Exception $e) {
        return '❌ Error during migration: ' . $e->getMessage();
    }
});

