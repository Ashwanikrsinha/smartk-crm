<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // -------------------------------------------------------
        // ROLE DEFINITIONS
        // DB name => Display label (set display_name if your roles
        // table has that column, else just use 'name')
        // -------------------------------------------------------

        $rolePermissions = [

            // --------------------------------------------------
            // 1. SALES PERSON (Operator)
            //    Own data only. Field operations.
            // --------------------------------------------------
            'Operator' => [
                // Schools
                'browse_customers',
                'create_customers',
                'show_customers',
                // Own schools editable (enforced in policy, not just permission)
                'edit_customers',

                // POs
                'browse_invoices',
                'create_invoices',
                'edit_invoices',
                'show_invoices',

                // Visit Logs
                'browse_visits',
                'create_visits',
                'edit_visits',
                'show_visits',
                'delete_visits',

                // PDCs (attached to PO)
                'browse_pdcs',
                'create_pdcs',
                'edit_pdcs',
                'delete_pdcs',

                // School Documents
                'browse_school_documents',
                'create_school_documents',

                // Lead Sources (read only)
                'browse_lead_sources',

                // Tasks
                'browse_tasks',
                'create_tasks',
                'show_tasks',
                'edit_tasks',

                // Leaves
                'browse_leaves',
                'create_leaves',
                'show_leaves',

                // Targets (own, read only)
                'browse_targets',
                'show_targets',

                // Products (read only for PO form)
                'browse_products',
                'browse_items',

                // Categories (read only for PO form)
                'browse_categories',
            ],

            // --------------------------------------------------
            // 2. SALES MANAGER (Manager)
            //    Team-scoped. Approves POs. Can create SPs.
            // --------------------------------------------------
            'Manager' => [
                // Schools (team-scoped)
                'browse_customers',
                'create_customers',
                'edit_customers',
                'show_customers',
                'delete_customers',

                // POs (team-scoped + approval)
                'browse_invoices',
                'create_invoices',
                'edit_invoices',
                'show_invoices',
                'delete_invoices',
                'approve_invoices',

                // Visit Logs (team-scoped)
                'browse_visits',
                'create_visits',
                'edit_visits',
                'show_visits',
                'delete_visits',

                // Bills (generate PDF order after approval)
                'browse_bills',
                'create_bills',
                'show_bills',

                // PDCs
                'browse_pdcs',
                'show_pdcs',

                // Lead Sources
                'browse_lead_sources',
                'create_lead_sources',
                'edit_lead_sources',

                // School Documents
                'browse_school_documents',
                'create_school_documents',

                // Team data access
                'view_team_data',

                // Can create SP accounts
                'create_sp_accounts',
                'browse_users',
                'create_users',
                'show_users',

                // Targets (assign to team SPs)
                'browse_targets',
                'create_targets',
                'edit_targets',
                'show_targets',
                'delete_targets',

                // Tasks
                'browse_tasks',
                'create_tasks',
                'edit_tasks',
                'show_tasks',
                'delete_tasks',

                // Leaves (approve team SP leaves)
                'browse_leaves',
                'edit_leaves',
                'show_leaves',

                // Reports & Export
                'export_reports',

                // Products (read only)
                'browse_products',
                'browse_items',
                'browse_categories',

                // Quotations
                'browse_quotations',
                'create_quotations',
                'edit_quotations',
                'show_quotations',
                'delete_quotations',
            ],

            // --------------------------------------------------
            // 3. ACCOUNTS TEAM (New role)
            //    Payment verification. Collection updates.
            // --------------------------------------------------
            'Accounts' => [
                // POs — read only, no create/edit/delete
                'browse_invoices',
                'show_invoices',

                // Collections — this is their primary job
                'browse_collections',
                'create_collections',
                'edit_collections',
                'show_collections',

                // Payment verification action
                'verify_payments',

                // Schools — read only
                'browse_customers',
                'show_customers',

                // Bills — view/generate invoices
                'browse_bills',
                'create_bills',
                'show_bills',

                // PDCs — view
                'browse_pdcs',
                'show_pdcs',

                // Reports & Export
                'export_reports',
                'view_all_orders',
            ],

            // --------------------------------------------------
            // 4. WAREHOUSE (New role)
            //    Dispatch tracking only.
            // --------------------------------------------------
            'Warehouse' => [
                // Approved POs — read only
                'browse_invoices',
                'show_invoices',

                // Dispatch queue
                'browse_dispatch_queue',
                'browse_inventory',
                'view_all_orders',

                // Dispatch logs
                'browse_dispatches',
                'create_dispatches',
                'edit_dispatches',
                'show_dispatches',
            ],

            // --------------------------------------------------
            // 5. ADMINISTRATOR
            //    Full system access. All permissions.
            // --------------------------------------------------
            'Administrator' => '*', // handled separately below

            'BusinessManager' => [
                // POs — view all, approve at BM level only
                'browse_invoices',
                'show_invoices',
                'bm_approve_invoices',
                'view_all_pos',

                // Schools — read only
                'browse_customers',
                'show_customers',

                // Visits — read only
                'browse_visits',
                'show_visits',

                // Reports — full export
                'export_reports',

                // Dashboard widgets (same as SM — full financials view)
                'view_all_data',       // gives full teamMemberIds() scope

                // Bills — view only (not create/edit)
                'browse_bills',

                // Targets — view only
                'browse_targets',
                'show_targets',

                // Leaves — view only
                'browse_leaves',
                'show_leaves',

                // Tasks — view only
                'browse_tasks',
                'show_tasks',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissions) {

            $role = Role::firstOrCreate(['name' => $roleName]);

            if ($permissions === '*') {
                // Admin gets every permission
                $allIds = Permission::pluck('id');
                $role->permissions()->sync($allIds);
                continue;
            }

            $permissionIds = Permission::whereIn('name', $permissions)->pluck('id');
            $role->permissions()->sync($permissionIds);
        }

        // Create Accounts and Warehouse roles if they don't exist
        Role::firstOrCreate(['name' => 'Accounts']);
        Role::firstOrCreate(['name' => 'Warehouse']);
    }
}
