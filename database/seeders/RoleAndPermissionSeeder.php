<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            'view-dashboard',
            'manage-users',
            'manage-roles-permissions',
            'view-all-audits',
            'view-reports',
            'create-audit',
            'upload-audit-files',
            'review-user-responses',
            'approve-audit',
            'reject-audit',
            'view-own-audits',
            'upload-response-evidence',
            'update-response-notes',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $roleSuperadmin = Role::firstOrCreate(['name' => 'Superadmin']);
        $roleSuperadmin->syncPermissions([
            'view-dashboard',
            'manage-users',
            'manage-roles-permissions',
            'view-all-audits',
            'view-reports',
        ]);

        $roleAuditor = Role::firstOrCreate(['name' => 'Auditor']);
        $roleAuditor->syncPermissions([
            'create-audit',
            'upload-audit-files',
            'review-user-responses',
            'approve-audit',
            'reject-audit',
        ]);

        $roleUser = Role::firstOrCreate(['name' => 'User']);
        $roleUser->syncPermissions([
            'view-own-audits',
            'upload-response-evidence',
            'update-response-notes',
        ]);

        // Create default users for each role
        // 1. Superadmin User
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
            ]
        );
        $superadmin->assignRole('Superadmin');

        // 2. Auditor User
        $auditor = User::firstOrCreate(
            ['email' => 'auditor@gmail.com'],
            [
                'name' => 'Auditor Utama',
                'password' => bcrypt('password'),
            ]
        );
        $auditor->assignRole('Auditor');

        // 3. User/Teller User 1 (17800T60)
        $teller = User::firstOrCreate(
            ['email' => 'teller60@gmail.com'],
            [
                'name' => 'Teller 60',
                'password' => bcrypt('password'),
                'phone' => '081234567890',
                'user_code' => '17800T60',
            ]
        );
        $teller->assignRole('User');

        // 4. User/Teller User 2 (17800CS63)
        $cs = User::firstOrCreate(
            ['email' => 'cs63@gmail.com'],
            [
                'name' => 'CS 63',
                'password' => bcrypt('password'),
                'phone' => '089876543210',
                'user_code' => '17800CS63',
            ]
        );
        $cs->assignRole('User');
    }
}
