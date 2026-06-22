# Pharmacy Management System: Roles, Features & UI Design

This document details the roles, system features, and user interface (UI) specifications for the Pharmacy Management System.

---

## 1. System Roles

The application supports **two distinct roles** to segregate duties between system administrators and data entry operators:

| Role | Target User | Description | Permissions |
| :--- | :--- | :--- | :--- |
| **Admin** | Pharmacy Owner / Store Manager | Full system administrator. Manages system configurations, audits data, and oversees user accounts. | Create, Read, Update, Delete (all records), View User Management, Access settings. |
| **Data Entry** | Data Entry Operator / Clerk | Operational staff responsible for registering medicines, companies, and suppliers. | Create, Read, Update (categories, locations, companies, suppliers). **Cannot Delete**. |

---

## 2. Complete Feature List

### A. Authentication & User Management
*   **Secure Authentication**: Secure login, registration, and logout flows powered by Laravel Breeze and Inertia.
*   **Role Selection**: Ability to assign/register users as either `Admin` or `Data Entry` operators.
*   **User Management (Admin Only)**: Admin dashboard list displaying all registered users, their emails, and active roles.
*   **Profile Settings**: Edit profile details (name, email) and secure password updates.

### B. Category Management
*   **Create Category**: Quick form to define new medicine categories (e.g., Tablets, Syrups, Capsules).
*   **View Categories**: Paginated table layout showing categories and their publishing status.
*   **Edit Category**: Modify existing category details.
*   **Delete Category (Admin Only)**: Completely remove categories from the system database.

### C. Location Rack Management
*   **Create Location**: Add storage locations and rack identifiers (e.g., Rack A-1, Cold Storage).
*   **View Locations**: Visual grid and list of all available storage locations.
*   **Edit Location**: Re-organize or update storage location tags.
*   **Delete Location (Admin Only)**: Delete storage racks when no longer in use.

### D. Medicine Company Management
*   **Create Company**: Add pharmaceutical manufacturing companies (e.g., Pfizer, Novartis).
*   **View Companies**: View list of active pharmaceutical suppliers/companies.
*   **Edit Company**: Update contact information, status, or addresses for manufacturers.
*   **Delete Company (Admin Only)**: Remove companies from the active roster.

### E. Medicine Supplier Management
*   **Create Supplier**: Record distributor/supplier contact numbers, addresses, and emails.
*   **View Suppliers**: Complete supplier directory with active search.
*   **Edit Supplier**: Update distributor information.
*   **Delete Supplier (Admin Only)**: Decommission and delete supplier entries.

---

## 3. UI Design Specifications

The UI is built using **Vue 3, Inertia.js, and Tailwind CSS** for a responsive, modern web experience.

### A. Sidebar Navigation (AsideBar)
*   **Universal Header**: Application branding with a user profile card and role badge.
*   **Dynamic Links**:
    *   *Shared Links*: `Dashboard`, `Category`, `Location Rack`, `Medicine Company`, `Medicine Supplier`.
    *   *Admin-Only Links*: `Settings`.
*   **User Role Display Badge**: A clean Tailwind pill badge under the user's name:
    *   `<span class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-0.5 rounded">Admin</span>`
    *   `<span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded">Data Entry</span>`

### B. Admin Dashboard UI
*   **System Stat Cards**: Three columns displaying quick statistics:
    1.  *New products this week* (Count & percentage delta).
    2.  *Visitors this week* (Analytical metrics).
    3.  *User signups this week* (System audit metrics).
*   **User Directory Table**: A clean user management card showing:
    *   Header: "System Users"
    *   Columns: ID, Name, Email, Role (Badge format), Created Date.

### C. Data Entry Dashboard UI
*   **Actionable Stat Cards**: Displays total counts of entered entities:
    *   `Total Categories` | `Total Location Racks` | `Total Companies` | `Total Suppliers`
*   **Quick Shortcuts Panel**: Grid of quick add actions for easy data population:
    *   `Quick Add Category` button (green gradient).
    *   `Quick Add Location` button (indigo gradient).
    *   `Quick Add Company` button (orange gradient).
    *   `Quick Add Supplier` button (pink gradient).
*   **Recent Entries Feed**: Display list of last 5 items entered in the database to prevent duplicate typing.

### D. Data Listing Pages (Index Views)
*   **Action Buttons segregation**:
    *   *Edit Action*: Pencil icon button (indigo background), visible to both roles.
    *   *Delete Action*: Trash can icon button (red background), **only visible to users logged in with the Admin role** (`v-if="$page.props.auth.user.role === 'admin'"`).

---

## 4. Visual Interfaces (Role UIs)

Here are the visual representations of each role's dashboard, medicine catalog showing storage locations, and sales register:

### A. Admin Portal UI
The Admin interface provides full user listings, total sales revenue numbers, and complete delete/refund actions.

#### Admin Dashboard View
Displays key metric counters (Total Medicines, Sales Logged, Total Revenue `$25.00`) and the registered user directory table.
![Admin Dashboard v2](file:///C:/Users/Sadman%20Islam/.gemini/antigravity-ide/brain/225da8a7-25cd-494e-a341-a870f043eb97/admin_dashboard_v2_1782103451275.png)

#### Admin Medicines Listing View
Shows full actions (Adjust Stock, Edit, and Delete) in the Medicines list.
![Admin Medicines v2](file:///C:/Users/Sadman%20Islam/.gemini/antigravity-ide/brain/225da8a7-25cd-494e-a341-a870f043eb97/admin_medicines_v2_1782103476690.png)

---

### B. Data Entry Operator Portal UI
The Data Entry interface focuses on entry, searching, stock adjustment, and sales recording. It hides deletion/refund capabilities.

#### Data Entry Dashboard View
Displays operator shortcuts (Record a Sale, Quick Add Medicine) and standard database metric counts (without displaying the revenue figure or user table).
![Data Entry Dashboard v2](file:///C:/Users/Sadman%20Islam/.gemini/antigravity-ide/brain/225da8a7-25cd-494e-a341-a870f043eb97/data_entry_dashboard_v2_1782103012769.png)

#### Data Entry Medicines View (Showing Storage Locations)
Displays the highlighted **Location Rack (Storage)** column (e.g. `Rack A-1`) next to each medicine so operators know exactly where to locate or stock the product.
![Data Entry Medicines v2](file:///C:/Users/Sadman%20Islam/.gemini/antigravity-ide/brain/225da8a7-25cd-494e-a341-a870f043eb97/data_entry_medicines_v2_1782103067483.png)

#### Data Entry Sales Register View
Lists logged sales. The action column does not show delete buttons and is marked as "No Actions" for operators.
![Data Entry Sales v2](file:///C:/Users/Sadman%20Islam/.gemini/antigravity-ide/brain/225da8a7-25cd-494e-a341-a870f043eb97/data_entry_sales_v2_1782103313129.png)

