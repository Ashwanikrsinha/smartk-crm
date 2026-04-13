# System Context: Specialized School CRM (SmartK)

## 1. Project Purpose & Scope
SmartK is a specialized CRM designed to streamline and govern the lifecycle of school-based sales operations. It provides a centralized platform connecting field agents (Sales Persons), management (Sales Managers), and financial controllers (Accounts Team).

### **Key Objectives**
- **Field Governance**: Tracking school visits and kit distributions.
- **Audit Workflow**: Ensuring every sales order is approved by management.
- **Financial Integrity**: Real-time visibility into collections vs. sales.

---

## 2. Technical Stack
- **Backend Framework**: Laravel 11.x (PHP 8.2+)
- **Database**: MySQL/PostgreSQL
- **Frontend**: Blade Templating, SCSS, JavaScript
- **Key Libraries**: 
    - [Yajra DataTables](https://yajrabox.com/docs/laravel-datatables/master) for high-performance listing.
    - [Laravel Excel](https://docs.laravel-excel.com/) for reporting.
    - Custom Role-Based Access Control (RBAC).

---

## 3. Core System Entities (Rebranded)
The system uses a repurposed architecture from a generic CRM to fit school-specific needs:

| Entity Name | Rebranded As | Primary Responsibility |
| :--- | :--- | :--- |
| **Customer** | **School** | Stores school details (City, State, Pin Code, Contact Person). |
| **Visit** | **Visit Log** | Records field visits, kits distributed, and follow-ups. |
| **Invoice** | **Sales Order (SO)** | Formal order generated after a successful visit. |
| **Bill** | **School Order PDF** | Professional document generated upon SO approval. |
| **Receipt** | **Collection Update** | Tracks payments received against Sales Orders. |

---

## 4. User Roles & Responsibility Matrix
Access is managed via a dynamic Permission-Role system.

| Role | Primary Responsibility | Data Access |
| :--- | :--- | :--- |
| **Sales Person (SP)** | Log visits, distribute kits, initiate orders. | Own data only. |
| **Sales Manager (SM)** | Audit pending orders, approve/reject SOs. | Team/Department data. |
| **Accounts Team** | Verify bank credits, update payment status. | Financial/Order data. |
| **Admin (Marketing)** | Manage users, lead sources, and global reports. | System-wide data. |

---

## 5. End-to-End Workflow

### **Step 1: Field Visit (Sales Person)**
- **Visit Log**: SP logs a visit in [Visit.php](file:///d:/Data/smartk-crm/app/Models/Visit.php). 
- **Kit Distribution**: Tracks kits (products) distributed via `visit_items`.
- **Order Initiation**: If a sale is made, SP generates a **Pending Sales Order** (Invoice).

### **Step 2: Managerial Audit (Sales Manager)**
- **Approval Queue**: SM reviews pending SOs in the dashboard.
- **Decision**: 
    - **Approve**: Formalizes the order; triggers PDF generation.
    - **Reject**: Returns order to SP with a mandatory rejection reason.

### **Step 3: Document Generation**
- Once approved, the system generates a professional **Printed Order** using the logic in [BillController.php](file:///d:/Data/smartk-crm/app/Http/Controllers/BillController.php).

### **Step 4: Revenue Reconciliation (Accounts)**
- **Collection**: Accounts team records payments against approved SOs.
- **Closure**: Dashboard updates to reflect total sales vs. total collection.

---

## 6. Technical Implementation Details

### **Authorization & Policies**
The system uses Laravel Policies located in [app/Policies/](file:///d:/Data/smartk-crm/app/Policies/).
- Policies call `$user->hasPermission('permission_name')`.
- Permissions are linked to Roles, and Roles are assigned to Users.

### **Data Relationships**
- **User -> Role**: Many-to-One.
- **Visit -> Customer (School)**: Many-to-One.
- **Invoice (SO) -> Visit**: One-to-One (Optional).
- **Invoice (SO) -> InvoiceItem**: One-to-Many.

### **Key Controllers**
- [VisitController.php](file:///d:/Data/smartk-crm/app/Http/Controllers/VisitController.php): Handles the core field visit logic.
- [InvoiceController.php](file:///d:/Data/smartk-crm/app/Http/Controllers/InvoiceController.php): Manages the Sales Order lifecycle (Create, Edit, Approve/Reject).
- [CustomerController.php](file:///d:/Data/smartk-crm/app/Http/Controllers/CustomerController.php): Manages the school database.

---

## 7. View & Layout Structure
The frontend is built using Laravel Blade templates with a modular structure.

### **Layout Architecture**
- **Main Layouts**: Located in [resources/views/layouts/](file:///d:/Data/smartk-crm/resources/views/layouts/).
    - `app.blade.php`: The primary layout for the application.
    - `dashboard.blade.php`: Specialized layout for dashboard views.
- **Partials**: Located in [resources/views/partials/](file:///d:/Data/smartk-crm/resources/views/partials/).
    - `sidebar.blade.php`: Dynamic navigation menu based on user permissions.
    - `top-nav.blade.php`: User profile, notifications, and search.
    - `errors.blade.php` & `success.blade.php`: Global alert handling.

### **Resource View Pattern**
Each module (Schools, Visits, Orders) follows a consistent view pattern:
- `index.blade.php`: DataTables listing.
- `create.blade.php` / `edit.blade.php`: Form wrappers.
- `form.blade.php`: Reusable form fields.
- `buttons.blade.php`: Action buttons (Edit, Delete, View) used inside DataTables.
- `show.blade.php`: Detailed view of a single record.

---

## 8. Database & Migration Flow
The database schema is designed for a multi-step sales process.

### **Migration Sequence**
1. **Foundation**: Roles, Permissions, and Users ([2014_10_12_000000_create_users_table.php](file:///d:/Data/smartk-crm/database/migrations/2014_10_12_000000_create_users_table.php)).
2. **Organizational**: Departments, Designations, and Employees.
3. **Core Entities**: Schools (Contacts), Products (Items), and Visit Types (Purposes).
4. **Transactions**: 
    - Visits and Visit Items (Teacher Kits).
    - Quotations and Sales Orders (Invoices).
    - Receipts and Payments (Collections).

### **Seeding Strategy**
- **PermissionSeeder**: Populates atomic permissions.
- **UserSeeder**: Creates default Admin and sample SP/SM accounts.
- **Role-Permission Mapping**: Permissions are attached to roles (Admin, Sales Manager, Sales Person, Accounts) during seeding.

---

## 9. Comprehensive Database Schema
The system's data model is fully documented in [ai_index.json](file:///d:/Data/smartk-crm/ai_index.json). Below are the primary tables and their key columns:

- **Users & Auth**: `users` (id, username, role_id, reportive_id), `roles`, `permissions`.
- **School Management**: `customers` (id, name, city, state, address, gst_number), `contacts` (customer_id, name, designation_id).
- **Field Operations**: `visits` (id, visit_number, customer_id, user_id, status, level), `visit_items` (visit_id, product_id, quantity).
- **Sales Lifecycle**: `invoices` (id, invoice_number, customer_id, visit_id, amount, status), `invoice_items`.
- **Financial Documents**: `bills` (id, bill_number, type, customer_id, total_amount, is_approved).
- **External Integration (Tally)**: `tally_sales`, `tally_payments`, `tally_receipts`.
- **Task & HR**: `tasks`, `leaves`, `targets`.

Refer to the `database_schema` section in [ai_index.json](file:///d:/Data/smartk-crm/ai_index.json) for the exhaustive list of all columns for every table.
