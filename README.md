# Specialized School CRM - SmartK

## Specialized CRM Overview
This system is a specialized CRM designed for managing school sales visits, kit distributions, and revenue collection. It transitioned from a generalized Master CRM to a lean, role-specific enterprise tool.

## Tech Stack
- **Framework**: Laravel 11.x (Upgraded from 8.x)
- **Language**: PHP 8.2+
- **Database**: MySQL / PostgreSQL
- **Frontend**: Blade, SCSS, JavaScript (DataTables integration)
- **Architecture**: Service-Oriented MVC

## Component Reuse & Repurpose Strategy
The existing "Master CRM" architecture provides a high degree of reusability (~75% of core logic). Below is the specific mapping for the specialized School CRM:

| Current Component | New Specialized Role | Reuse Magnitude | Specific Modification |
| :--- | :--- | :--- | :--- |
| **Customer** | **School** | High (90%) | Rename table/model; Add `city`, `state`, `pincode`, and `lead_source_id`. |
| **Invoice** | **Sales Order (SO)** | High (80%) | Change label to "Order"; Add `sm_approval_status` and `sm_rejection_comment`. |
| **Bill (Print)** | **School Order PDF** | High (85%) | Reuse professional layout for printed forms; Remove generic transport info if not needed. |
| **Visit** | **Visit Log** | High (95%) | Add fields for "Kits Distributed" and "Cheque/Payment Captured". |
| **Payment/Receipt** | **Collection Update** | Medium (60%) | Link directly to `Sales Order` instead of generic `Ledger`. |

## Specialized Workflow Flow
1. **Field Action (SP)**: Sales Person visits a School, logs it in `Visit Log`, and if interested, generates a `Sales Order` (repurposed from `Invoice`).
2. **Managerial Audit (SM)**: Sales Manager sees a dashboard of "Pending Orders". They can Approve (releasing for print) or Reject.
3. **School Document (Print)**: Once SM approves, the `Printed Order` (repurposed from `Bill`) is generated. This is the official document for the school.
4. **Revenue Tracking (Accounts)**: 
   - Accounts Team uses a specialized view to see all "Approved Orders".
   - They record payments received against these specific `Order IDs`.
   - The dashboard dynamically calculates **Total Sale Amount** vs **Total Collection** vs **Pending Amount**.

## Technical Alignment Summary
- **UI Reuse**: Existing Datatables and Form structures for Invoices will be rebranded as Sales Orders.
- **Database Reuse**: The `invoice_items` table structure is perfect for `order_items` (kits, quantities, rates).
- **Print Reuse**: The existing PDF/Print logic in `Bill` will be specialized for the final School Order format.

## Installation (PHP 8.2+)
1. Clone the repository.
2. Run `composer install`.
3. Copy `.env.example` to `.env` and configure database credentials.
4. Run `php artisan key:generate`.
5. Run `php artisan migrate --seed`.

## Environment Setup
- `APP_ENV`: local/production
- `DB_CONNECTION`: mysql
- `QUEUE_CONNECTION`: redis/database (for background reports)

## Migration & Seeding
The system uses specialized migrations for:
- `schools` (formerly customers)
- `visits` (with payment and approval fields)
- `kits` (specialized product sets)
- `lead_sources`

Run `php artisan db:seed` to populate initial roles and permissions.

## Queue Setup
The CRM uses Laravel Queues for:
- Visit Export generation
- Manager notification emails
- Marketing report aggregation

```bash
php artisan queue:work
```

## Deployment
1. Ensure PHP 8.2+ is installed.
2. Set up a process manager (Supervisor) for the queue worker.
3. Configure Nginx/Apache to point to the `public` directory.
4. Optimize for production:
```bash
php artisan optimize
php artisan view:cache
```

## Upgrade Notes (From Laravel 8)
- Middleware is now handled in `bootstrap/app.php`.
- Config files are simplified and can be published individually.
- Eloquent models use stricter typing.
- Built-in CORS and Proxy handling (removed external packages).

## Known Technical Debt
- **Coupling**: Some legacy dependencies between `Visit` and the removed `Invoice` module may exist in migrations.
- **N+1**: Certain dashboard widgets require query optimization for large datasets (50k+ users).

## Contribution Guide
1. Follow PSR-12 coding standards.
2. Ensure all new features are covered by Feature Tests.
3. Document any schema changes in the `/docs` folder.
