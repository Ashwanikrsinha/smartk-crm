<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('permissions')->delete();

        $crudModules = [
            'bills',
            'categories',
            'customer_types',
            'customers',       // Schools
            'departments',
            'designations',
            'employees',
            'events',
            'groups',
            'invoices',        // Purchase Orders (POs)
            'leaves',
            'logs',
            'news',
            'permissions',
            'products',
            'purposes',
            'quotations',
            'roles',
            'segments',
            'states',
            'supplier_types',
            'suppliers',
            'targets',
            'tasks',
            'transports',
            'units',
            'users',
            'visits',
            'challans',
            'collections',
            'lead_sources',
            'pdcs',
            'school_documents',
            'dispatch_queue',
            'inventory',
        ];

        $permissions = [];

        foreach ($crudModules as $module) {
            $permissions[] = ['name' => "browse_{$module}"];
            $permissions[] = ['name' => "create_{$module}"];
            $permissions[] = ['name' => "edit_{$module}"];
            $permissions[] = ['name' => "delete_{$module}"];
            $permissions[] = ['name' => "show_{$module}"];
        }

        // -------------------------------------------------------
        // Special action permissions (not simple CRUD)
        // -------------------------------------------------------
        $actionPermissions = [
            // Sales Manager
            'approve_invoices',        // Approve/reject POs
            'view_team_data',          // Scope to own team's SPs
            'create_sp_accounts',      // SM can create SP user accounts

            // Accounts Team
            'verify_payments',         // Mark collection as received
            'view_all_orders',         // See all approved POs

            // Admin / Reporting
            'export_reports',          // Excel/PDF export
            'view_all_data',           // Full system access (Admin)
            'edit_companies',          // Company settings

            // Items
            'edit_items',
            'browse_items',
            // Business Manager
            'bm_approve_invoices',     // BM: final approval of sm_approved POs
            'view_all_pos',            // BM: sees all POs across all SMs/teams

        ];

        foreach ($actionPermissions as $perm) {
            $permissions[] = ['name' => $perm];
        }

        DB::table('permissions')->insert($permissions);
    }
}
