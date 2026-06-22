<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Location;
use App\Models\Company;
use App\Models\Supplier;
use App\Models\Medicine;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 1. Seed Users
        User::create([
            'name' => env('SEED_ADMIN_NAME', 'Aroggo Admin'),
            'email' => env('SEED_ADMIN_EMAIL', 'admin@aroggo.com'),
            'password' => Hash::make(env('SEED_ADMIN_PASSWORD', 'aroggoadmin123')),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Data Entry User',
            'email' => 'data@example.com',
            'password' => Hash::make('password123'),
            'role' => 'data_entry',
        ]);

        User::create([
            'name' => 'Standard Customer',
            'email' => 'customer@example.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'next_purchase_discount' => 15, // Give standard customer a starting 15% discount
        ]);

        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        // 2. Seed Categories
        $categories = ['Tablet', 'Capsule', 'Syrup', 'Suspension', 'Inhaler', 'Injection'];
        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat,
                'status' => true,
            ]);
        }

        // 3. Seed Locations
        $locations = ['Rack A-1', 'Rack A-2', 'Rack B-1', 'Rack B-2', 'Cold Storage', 'Counter Shell'];
        foreach ($locations as $loc) {
            Location::create([
                'name' => $loc,
                'status' => true,
            ]);
        }

        // 4. Seed Companies
        $companies = [
            ['name' => 'Pfizer', 'short_name' => 'PFI', 'status' => true],
            ['name' => 'Novartis', 'short_name' => 'NOV', 'status' => true],
            ['name' => 'GSK', 'short_name' => 'GSK', 'status' => true],
            ['name' => 'Sanofi', 'short_name' => 'SAN', 'status' => true],
            ['name' => 'Square Pharmaceuticals', 'short_name' => 'SQU', 'status' => true],
            ['name' => 'Incepta', 'short_name' => 'INC', 'status' => true],
        ];
        foreach ($companies as $comp) {
            Company::create($comp);
        }

        // 5. Seed Suppliers
        $suppliers = [
            [
                'name' => 'Apex Pharma Distributors',
                'address' => 'Dhaka, Bangladesh',
                'contact_no' => '01711122233',
                'email' => 'apex@distributors.com',
                'status' => 1,
            ],
            [
                'name' => 'Global Medisource',
                'address' => 'Chittagong, Bangladesh',
                'contact_no' => '01822233344',
                'email' => 'contact@globalmedi.com',
                'status' => 1,
            ],
            [
                'name' => 'Lazz Pharma Ltd',
                'address' => 'Dhanmondi, Dhaka',
                'contact_no' => '01933344455',
                'email' => 'info@lazzpharma.com',
                'status' => 1,
            ]
        ];
        foreach ($suppliers as $sup) {
            Supplier::create($sup);
        }

        // 6. Seed Medicines
        $medicines = [
            [
                'name' => 'Napa Extend 665mg',
                'company_id' => 5, // Square Pharmaceuticals
                'category_id' => 1, // Tablet
                'location_id' => 1, // Rack A-1
                'pack_details' => '10 strips x 10 tablets',
                'quantity' => 150,
                'price' => 15.00,
                'status' => true,
            ],
            [
                'name' => 'Alatrol 10mg',
                'company_id' => 6, // Incepta
                'category_id' => 1, // Tablet
                'location_id' => 2, // Rack A-2
                'pack_details' => '15 strips x 10 tablets',
                'quantity' => 250,
                'price' => 3.50,
                'status' => true,
            ],
            [
                'name' => 'Tusca Syrup 100ml',
                'company_id' => 5, // Square Pharmaceuticals
                'category_id' => 3, // Syrup
                'location_id' => 5, // Cold Storage
                'pack_details' => '1 bottle x 100ml',
                'quantity' => 8, // Trigger low stock warning!
                'price' => 85.00,
                'status' => true,
            ],
            [
                'name' => 'Seclo 20mg Capsule',
                'company_id' => 5, // Square Pharmaceuticals
                'category_id' => 2, // Capsule
                'location_id' => 3, // Rack B-1
                'pack_details' => '10 strips x 10 capsules',
                'quantity' => 400,
                'price' => 5.00,
                'status' => true,
            ],
            [
                'name' => 'Pantocid 20mg',
                'company_id' => 6, // Incepta
                'category_id' => 1, // Tablet
                'location_id' => 3, // Rack B-1
                'pack_details' => '10 strips x 10 tablets',
                'quantity' => 300,
                'price' => 7.00,
                'status' => true,
            ],
            [
                'name' => 'Seretide 125 Inhaler',
                'company_id' => 3, // GSK
                'category_id' => 5, // Inhaler
                'location_id' => 4, // Rack B-2
                'pack_details' => '1 inhaler x 120 doses',
                'quantity' => 25,
                'price' => 850.00,
                'status' => true,
            ],
            [
                'name' => 'Amoxil 500mg Capsule',
                'company_id' => 3, // GSK
                'category_id' => 2, // Capsule
                'location_id' => 1, // Rack A-1
                'pack_details' => '10 strips x 10 capsules',
                'quantity' => 180,
                'price' => 8.00,
                'status' => true,
            ],
            [
                'name' => 'Lipitor 20mg',
                'company_id' => 1, // Pfizer
                'category_id' => 1, // Tablet
                'location_id' => 2, // Rack A-2
                'pack_details' => '3 strips x 10 tablets',
                'quantity' => 90,
                'price' => 40.00,
                'status' => true,
            ],
            [
                'name' => 'Norvasc 5mg',
                'company_id' => 1, // Pfizer
                'category_id' => 1, // Tablet
                'location_id' => 2, // Rack A-2
                'pack_details' => '3 strips x 10 tablets',
                'quantity' => 150,
                'price' => 12.00,
                'status' => true,
            ],
            [
                'name' => 'Solu-Medrol 500mg Injection',
                'company_id' => 1, // Pfizer
                'category_id' => 6, // Injection
                'location_id' => 5, // Cold Storage
                'pack_details' => '1 vial',
                'quantity' => 15,
                'price' => 450.00,
                'status' => true,
            ],
            [
                'name' => 'Galvus Met 50/850mg',
                'company_id' => 2, // Novartis
                'category_id' => 1, // Tablet
                'location_id' => 1, // Rack A-1
                'pack_details' => '3 strips x 10 tablets',
                'quantity' => 110,
                'price' => 22.00,
                'status' => true,
            ],
            [
                'name' => 'Tegretol CR 200mg',
                'company_id' => 2, // Novartis
                'category_id' => 1, // Tablet
                'location_id' => 4, // Rack B-2
                'pack_details' => '5 strips x 10 tablets',
                'quantity' => 80,
                'price' => 18.00,
                'status' => true,
            ],
            [
                'name' => 'Lantus Solostar 100 U/ml',
                'company_id' => 4, // Sanofi
                'category_id' => 6, // Injection
                'location_id' => 5, // Cold Storage
                'pack_details' => '5 prefilled pens x 3ml',
                'quantity' => 12,
                'price' => 3200.00,
                'status' => true,
            ],
            [
                'name' => 'Flagyl 400mg',
                'company_id' => 4, // Sanofi
                'category_id' => 1, // Tablet
                'location_id' => 1, // Rack A-1
                'pack_details' => '10 strips x 10 tablets',
                'quantity' => 500,
                'price' => 3.00,
                'status' => true,
            ],
            [
                'name' => 'Fenadin 120mg',
                'company_id' => 6, // Incepta
                'category_id' => 1, // Tablet
                'location_id' => 2, // Rack A-2
                'pack_details' => '5 strips x 10 tablets',
                'quantity' => 220,
                'price' => 8.00,
                'status' => true,
            ],
            [
                'name' => 'Ace Plus',
                'company_id' => 5, // Square Pharmaceuticals
                'category_id' => 1, // Tablet
                'location_id' => 1, // Rack A-1
                'pack_details' => '10 strips x 10 tablets',
                'quantity' => 600,
                'price' => 4.00,
                'status' => true,
            ],
            [
                'name' => 'Windel Syrup 100ml',
                'company_id' => 6, // Incepta
                'category_id' => 3, // Syrup
                'location_id' => 4, // Rack B-2
                'pack_details' => '1 bottle x 100ml',
                'quantity' => 50,
                'price' => 65.00,
                'status' => true,
            ],
            [
                'name' => 'Gaviscon Suspension 150ml',
                'company_id' => 3, // GSK
                'category_id' => 4, // Suspension
                'location_id' => 3, // Rack B-1
                'pack_details' => '1 bottle x 150ml',
                'quantity' => 35,
                'price' => 180.00,
                'status' => true,
            ],
            [
                'name' => 'Clexane 60mg',
                'company_id' => 4, // Sanofi
                'category_id' => 6, // Injection
                'location_id' => 5, // Cold Storage
                'pack_details' => '2 syringes',
                'quantity' => 18,
                'price' => 950.00,
                'status' => true,
            ]
        ];

        foreach ($medicines as $medData) {
            Medicine::create($medData);
        }
    }
}
