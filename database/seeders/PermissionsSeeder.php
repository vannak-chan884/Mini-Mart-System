<?php

// File: database/seeders/PermissionsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Insert all permission keys ─────────────────────────
        $permissions = [

            // Dashboard
            ['key' => 'dashboard.view', 'label' => 'View Dashboard', 'group' => 'Dashboard'],

            // POS
            ['key' => 'pos.view', 'label' => 'Access POS', 'group' => 'POS'],

            // Sales
            ['key' => 'sales.view', 'label' => 'View Sales', 'group' => 'Sales'],

            // Products
            ['key' => 'products.view', 'label' => 'View Products', 'group' => 'Products'],
            ['key' => 'products.create', 'label' => 'Create Products', 'group' => 'Products'],
            ['key' => 'products.edit', 'label' => 'Edit Products', 'group' => 'Products'],
            ['key' => 'products.delete', 'label' => 'Delete Products', 'group' => 'Products'],

            // Categories
            ['key' => 'categories.view', 'label' => 'View Categories', 'group' => 'Categories'],
            ['key' => 'categories.create', 'label' => 'Create Categories', 'group' => 'Categories'],
            ['key' => 'categories.edit', 'label' => 'Edit Categories', 'group' => 'Categories'],
            ['key' => 'categories.delete', 'label' => 'Delete Categories', 'group' => 'Categories'],

            // Expenses
            ['key' => 'expenses.view', 'label' => 'View Expenses', 'group' => 'Expenses'],
            ['key' => 'expenses.create', 'label' => 'Create Expenses', 'group' => 'Expenses'],
            ['key' => 'expenses.edit', 'label' => 'Edit Expenses', 'group' => 'Expenses'],
            ['key' => 'expenses.delete', 'label' => 'Delete Expenses', 'group' => 'Expenses'],

            // Expense Categories
            ['key' => 'expense_categories.view', 'label' => 'View Expense Categories', 'group' => 'Expense Categories'],
            ['key' => 'expense_categories.create', 'label' => 'Create Expense Categories', 'group' => 'Expense Categories'],
            ['key' => 'expense_categories.edit', 'label' => 'Edit Expense Categories', 'group' => 'Expense Categories'],
            ['key' => 'expense_categories.delete', 'label' => 'Delete Expense Categories', 'group' => 'Expense Categories'],

            // Users
            ['key' => 'users.view', 'label' => 'View Users', 'group' => 'Users'],
            ['key' => 'users.create', 'label' => 'Create Users', 'group' => 'Users'],
            ['key' => 'users.edit', 'label' => 'Edit Users', 'group' => 'Users'],
            ['key' => 'users.delete', 'label' => 'Delete Users', 'group' => 'Users'],

        ];

        foreach ($permissions as $perm) {
            DB::table('permissions')->updateOrInsert(
                ['key' => $perm['key']],
                array_merge($perm, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // ── 2. Default cashier permissions ────────────────────────
        // Sensible starting point — admin can adjust later via the UI
        $cashierDefaults = [
            'dashboard.view',
            'pos.view',
            'sales.view',
            'products.view',
            'categories.view',
            'expenses.view',
            'expense_categories.view',
        ];

        foreach ($cashierDefaults as $key) {
            DB::table('role_permissions')->updateOrInsert(
                ['role' => 'cashier', 'permission_key' => $key],
                [
                    'role' => 'cashier',
                    'permission_key' => $key,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}