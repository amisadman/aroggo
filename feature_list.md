# 📝 Aroggo Pharmacy - Feature List & Specifications

This document outlines the detailed features, access control matrix, database relationships, and interactive user flows of the **Aroggo Pharmacy Management System**.

---

## 🔑 1. User Roles & Permissions Matrix

The system separates users into three distinct roles. Access rules are enforced via Laravel controllers and Inertia page-level checks.

| Feature Area | Admin | Data Entry (Operator) | Customer |
| :--- | :---: | :---: | :---: |
| **View Dashboard Metrics** | Full (Revenue + Counts) | Counts Only | Personal Purchases Only |
| **Register Staff Accounts** | Yes | No | No |
| **Manage Users & Delete Accounts** | Yes | No | No |
| **Manage Categories (Create/Edit)** | Yes | Yes | No |
| **Delete Categories** | Yes | No | No |
| **Manage Location Racks (Create/Edit)** | Yes | Yes | No |
| **Delete Location Racks** | Yes | No | No |
| **Manage Companies (Create/Edit)** | Yes | Yes | No |
| **Delete Companies** | Yes | No | No |
| **Manage Suppliers (Create/Edit)** | Yes | Yes | No |
| **Delete Suppliers** | Yes | No | No |
| **Manage Medicines (Create/Edit/Adjust Stock)** | Yes | Yes | No |
| **Bulk Import Medicines (CSV)** | Yes | Yes | No |
| **Delete Medicines** | Yes | No | No |
| **Log Sales Transactions** | Yes | Yes | No |
| **Delete Sales (Refund Stock)** | Yes | No | No |
| **Review & Approve Prescriptions** | Yes | Yes | No |
| **Upload Prescriptions** | No | No | Yes |
| **Download & Print Sales Invoices** | Yes | Yes | Yes (Own invoices) |

---

## 🛠️ 2. Core Functional Modules

### A. Authentication & Registration Flow
*   **Public Signup (Customer Default)**: The standard registration page registers users as `customer` by default. There is no public role selection dropdown, preventing unauthorized administrative credentials.
*   **Admin-Seeded Setup**: The system admin account is seeded directly from environment variable settings (`.env`) during initial database migration.
*   **Administrative Staff Panel**: Authenticated Admins have a form in the dashboard to register new Operators or other Admins safely without logging the active admin user out of the session.

### B. Master Catalog Configurations
*   **Category Management**: Group medications into classifications (e.g. *Tablets*, *Injections*, *Syrups*).
*   **Location Rack Management**: Configure physical shelf slots (e.g., *Rack A-1*, *Cold Storage*) and associate them with medicines to guide technicians in retrieval.
*   **Pharmaceutical Company Logs**: Track major manufacturers (e.g., *Pfizer*, *Square Pharmaceuticals*).
*   **Supplier Directory**: Store distributor details, emails, and phone numbers to facilitate restocking.

### C. Medicine Inventory Control
*   **Medicine Registry**: Track detailed logs: name, manufacturing company, category, storage location, packaging description, quantity, and unit price.
*   **Stock Adjustment**: Inline adjustment triggers to restock or trim item quantities.
*   **Low Stock Indicator**: Visual alert tags appear in tables and dashboards for medicines whose inventory drops below 10 units.
*   **CSV Import Wizard**: Allows operators to batch-insert dozens of new medicine records instantly by uploading a `.csv` formatted file containing required header keys.

### D. Billing, Transactions, & Discount Engine
*   **Sale Recorder form**: Select target medicine and enter quantity. Displays current unit price and auto-calculated subtotal.
*   **Interactive Customer Search**: Autocomplete drop-down search allows staff to link the sale to registered customers.
*   **Loyalty Discounts**:
    *   Admins can adjust the next-purchase discount percentage of any customer directly from the user dashboard.
    *   During a sale, selecting a customer automatically queries the database, displays the discount percentage, and deducts the amount from the final total.
*   **Stock Reservation Validation**: Prevents completing transactions if requested sales quantities exceed available stock. Completing a sale automatically decrements inventory count.
*   **Delete & Rollback**: Admins can delete sales, which automatically returns the sold quantities back to the medicine inventory.

### E. Printable Receipts & Invoice Downloads
*   **Monospace Receipt Card**: Displays details styled like traditional thermal paper invoices.
*   **Thermal Printer Routing**: Integrated CSS `@media print` layout overrides that isolate the printable receipt layout and trigger the browser print dialogue.
*   **Invoice File Download**: Exports client receipt text as a downloadable structured `.txt` file containing receipt header, invoice number, items, taxes/discounts applied, and grand totals.

### F. Digital Prescription Center
*   **Customer Upload Uploader**: Customers can upload image copies of prescriptions.
*   **Verification Tracking**: Tracks status (`pending`, `approved`, `rejected`).
*   **Operator Approval**: Admins and operators review uploaded image files in their dashboard and mark them as approved once medication items have been prepared.

---

## 🎨 3. Visual & Aesthetic Architecture

*   **Color Tokens**:
    *   Primary Backgrounds & Navigation: `#1f2b5b` (Deep Navy)
    *   Highlights, Links, and Secondary Buttons: `#3dbdec` (Cyan)
    *   Success/Revenue: Soft green alerts
    *   Danger/Deletion: Soft red buttons
*   **Asset References**:
    *   The primary brand logo is located at `/assets/aroggo_pharmacy.png` and is integrated into navbar headers, login panels, and printable receipts.
    *   The user profile mockup avatar is loaded from `/assets/user.png`.
*   **Layout Structure**: Responsive two-column dashboard layout with dynamic collapsing sidebars, clean status badges, and table designs built on Tailwind UI.
