<p align="center">
  <img src="public/assets/aroggo_pharmacy.png" width="100" alt="Aroggo Pharmacy Logo" />
</p>

<h1 align="center">
  <span style="color: #1f2b5b;">Aroggo</span> <span style="color: #3dbdec;">Pharmacy</span>
</h1>

<p align="center">
  <strong>Pharmacy Management System</strong>
</p>

A premium, secure, and modern web-based Pharmacy Management System designed to handle medicine inventories, manage supplier logs, process sales transactions with automatic customer discounts, and track prescription uploads.

Designed with a rich, professional interface matching Aroggo Pharmacy's visual brand identity.

---

## 🎨 Branding & Visual Identity

*   **Primary Navy Blue**: `#1f2b5b` (For solid structural headers, dark backgrounds, and prominent navigation buttons)
*   **Accent Cyan**: `#3dbdec` (For hover effects, badges, highlight borders, and secondary accents)
*   **Typography**: *Inter Font Family* loaded globally for modern readability.
*   **Theme**: Fully responsive, clean layouts across admin, staff, and customer views with subtle background medical icon decorations.

---

## 🚀 Key Features

### 👤 Role-Based Access Control (RBAC)
The system supports three distinct user roles with custom interfaces and route guards:
*   **Admin (Pharmacy Owner/Manager)**:
    *   Full privileges to create, read, update, and delete all records.
    *   **Staff Creation Panel**: Dedicated dashboard panel to create new `Admin` and `Data Entry` operator accounts without logging out.
    *   **User Directory**: List and manage all registered accounts (including deletion).
    *   **Revenue Auditing**: Overview of total revenue, system metrics, and customer leaderboards.
*   **Data Entry (Staff/Operator)**:
    *   Full operational access to register medicines, categories, locations, companies, and suppliers.
    *   **CSV Medicine Importer**: Bulk upload medicine records.
    *   **Sales Register**: Log sales transactions, select customers, and automatically apply discounts.
    *   **Stock Control**: Adjust inventory and receive visual notifications for low-stock items.
    *   *Constraint*: Forbidden from deleting records or managing user accounts.
*   **Customer**:
    *   Publicly register, log in, and view personal transaction receipts.
    *   **Prescription Portal**: Upload handwritten/digital prescription images for pharmacy verification and track approval status.

### 📦 Inventory & Rack Management
*   **Categories**: Group medicines (Tablets, Capsules, Injections, Inhalers, Syrups).
*   **Rack Locations**: Organize shelf storage labels (e.g. *Rack A-1*, *Cold Storage*) so operators find medicines instantly.
*   **Manufacturers & Suppliers**: Track details for pharmaceutical companies (e.g., Pfizer, Novartis, Square) and local distributors.

### 💰 Intelligent Sales & Invoice System
*   **Inline Customer Search**: Search existing customers dynamically during sale registry.
*   **Automated Discount Engine**: Fetch customer-specific discount values and apply them to the transaction subtotal.
*   **Printable Receipts**: Styled, monospace receipt modal layout formatted like thermal printing.
*   **TXT Invoice Downloads**: One-click download of invoice records in a structured `.txt` layout for archiving.

---

## 🛠️ Technology Stack

*   **Backend**: [Laravel 9+](https://laravel.com)
*   **Frontend**: [Vue 3](https://vuejs.org) with Composition API
*   **Bridging**: [Inertia.js](https://inertiajs.com) (No API endpoints or client-side routers required; smooth SPA transition)
*   **Styles**: [Tailwind CSS](https://tailwindcss.com) (Standardized utility design system)
*   **Database**: MySQL (Compatible with MariaDB)
*   **Build Tool**: Vite

---

## ⚙️ Installation & Setup

Follow these steps to run the project locally on your system:

### Prerequisite Checklist
Ensure you have the following installed:
*   PHP (Version 8.0 or higher)
*   Composer
*   Node.js & NPM
*   MySQL/MariaDB Database Server

### Step 1: Clone & Navigate
```bash
git clone https://github.com/amisadman/aroggo.git
cd aroggo
```

### Step 2: Install Dependencies
Install PHP packages:
```bash
composer install
```
Install Javascript packages:
```bash
npm install
```

### Step 3: Environment Setup
Duplicate the example environment file:
```bash
cp .env.example .env
```
Open `.env` and configure your database settings:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306      # Or 3307 based on local setup
DB_DATABASE=aroggo_pharmacy
DB_USERNAME=root
DB_PASSWORD=
```
Add the initial Admin account seed credentials in `.env`:
```env
SEED_ADMIN_NAME="Aroggo Admin"
SEED_ADMIN_EMAIL="admin@aroggo.com"
SEED_ADMIN_PASSWORD="aroggoadmin123"
```

### Step 4: Database Migrations & Seeding
Create the database in your MySQL client, then run migrations and seed initial data (which includes locations, categories, suppliers, companies, and an expanded inventory of 20 medicines):
```bash
php artisan migrate:fresh --seed
```

### Step 5: Build Frontend Assets
Build production assets using Vite:
```bash
npm run build
```
*(Alternatively, run `npm run dev` to launch the Vite hot-reloading server)*

### Step 6: Start Server
Run the local development server:
```bash
php artisan serve
```
Now access the web portal at: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 🔐 Default Logins (Seeded)

Use these credentials to test different interfaces:

| Role | Email | Password | Features |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@aroggo.com` | `aroggoadmin123` | Staff registry, Delete actions, Leaderboard view |
| **Data Entry** | `data@example.com` | `password123` | Catalog entry, sales logs, CSV import |
| **Customer** | `customer@example.com` | `password123` | Purchase history, Prescription uploader (Includes 15% discount) |

---

## 🗄️ Core Database Migrations

*   `create_users_table`: Stores standard user profiles, including custom `role` (`admin`, `data_entry`, `customer`) and `next_purchase_discount` percentages.
*   `create_categories_table`: Stores name and status of medicine types.
*   `create_locations_table`: Stores shelf storage names (racks).
*   `create_companies_table`: Stores pharmaceutical manufacturer listings.
*   `create_suppliers_table`: Stores regional distributors.
*   `create_medicines_table`: Links company, category, location, and tracks `quantity` and `price`.
*   `create_sales_table`: Records medicine transactions, quantities sold, total prices, and links to customers.
*   `create_prescriptions_table`: Holds user uploaded prescription files and approval statuses.
