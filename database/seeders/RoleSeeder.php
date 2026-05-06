<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $rolePermissions = [

            // --------------------------------------------------
            // 1. SALES PERSON (Operator)
            // --------------------------------------------------
            'Operator' => [
                'browse_customers',
                'create_customers',
                'show_customers',
                'edit_customers',
                'browse_invoices',
                'create_invoices',
                'edit_invoices',
                'show_invoices',
                'browse_visits',
                'create_visits',
                'edit_visits',
                'show_visits',
                'delete_visits',
                'browse_pdcs',
                'create_pdcs',
                'edit_pdcs',
                'delete_pdcs',
                'browse_school_documents',
                'create_school_documents',
                'browse_lead_sources',
                'browse_tasks',
                'create_tasks',
                'show_tasks',
                'edit_tasks',
                'browse_leaves',
                'create_leaves',
                'show_leaves',
                'browse_targets',
                'show_targets',
                'browse_products',
                'browse_items',
                'browse_categories',
            ],

            // --------------------------------------------------
            // 2. SALES MANAGER (Manager)
            // --------------------------------------------------
            'Manager' => [
                'browse_customers',
                'create_customers',
                'edit_customers',
                'show_customers',
                'delete_customers',
                'browse_invoices',
                'create_invoices',
                'edit_invoices',
                'show_invoices',
                'delete_invoices',
                'approve_invoices',
                'browse_visits',
                'create_visits',
                'edit_visits',
                'show_visits',
                'delete_visits',
                'browse_bills',
                'create_bills',
                'show_bills',
                'browse_pdcs',
                'show_pdcs',
                'browse_lead_sources',
                'create_lead_sources',
                'edit_lead_sources',
                'browse_school_documents',
                'create_school_documents',
                'view_team_data',
                'create_sp_accounts',
                'browse_users',
                'create_users',
                'show_users',
                'browse_targets',
                'create_targets',
                'edit_targets',
                'show_targets',
                'delete_targets',
                'browse_tasks',
                'create_tasks',
                'edit_tasks',
                'show_tasks',
                'delete_tasks',
                'browse_leaves',
                'edit_leaves',
                'show_leaves',
                'export_reports',
                'browse_products',
                'browse_items',
                'browse_categories',
                'browse_quotations',
                'create_quotations',
                'edit_quotations',
                'show_quotations',
                'delete_quotations',
            ],

            // --------------------------------------------------
            // 3. ACCOUNTS
            // --------------------------------------------------
            'Accounts' => [
                'browse_invoices',
                'show_invoices',
                'browse_collections',
                'create_collections',
                'edit_collections',
                'show_collections',
                'verify_payments',
                'browse_customers',
                'show_customers',
                'browse_bills',
                'create_bills',
                'show_bills',
                'browse_pdcs',
                'show_pdcs',
                'export_reports',
                'view_all_orders',
            ],

            // --------------------------------------------------
            // 4. WAREHOUSE
            // --------------------------------------------------
            'Warehouse' => [
                'browse_invoices',
                'show_invoices',
                'browse_dispatch_queue',
                'browse_inventory',
                'view_all_orders',
                'browse_dispatches',
                'create_dispatches',
                'edit_dispatches',
                'show_dispatches',
            ],

            // --------------------------------------------------
            // 5. MARKETING  ← NEW ROLE
            //    Read-only: schools (basics) + PO number/date/amount.
            //    No collection, no outstanding, no master data entry.
            // --------------------------------------------------
            'Marketing' => [
                // Schools — read only, basic fields only
                'browse_customers',
                'show_customers',

                // POs — read only (amount visible, collection/outstanding hidden in view layer)
                'browse_invoices',
                'show_invoices',

                // Lead sources — read only (for filter)
                'browse_lead_sources',

                // Export
                'export_reports',
            ],

            // --------------------------------------------------
            // 6. ADMINISTRATOR — all permissions
            // --------------------------------------------------
            'Administrator' => '*',

            // --------------------------------------------------
            // 7. BUSINESS MANAGER
            // --------------------------------------------------
            'BusinessManager' => [
                'browse_invoices',
                'show_invoices',
                'bm_approve_invoices',
                'view_all_pos',
                'browse_customers',
                'show_customers',
                'browse_visits',
                'show_visits',
                'export_reports',
                'view_all_data',
                'browse_bills',
                'browse_targets',
                'show_targets',
                'browse_leaves',
                'show_leaves',
                'browse_tasks',
                'show_tasks',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            if ($permissions === '*') {
                $role->permissions()->sync(Permission::pluck('id'));
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
