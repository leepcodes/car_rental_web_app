<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Dashboard
            'view dashboard',
            
            // Profile Management
            'manage own profile',
            'manage any profile',
            
            // Vehicle Management
            'manage own vehicles',
            'manage any vehicles',
            'verify vehicles',
            
            // Booking Management
            'manage own bookings',
            'manage booking requests',
            'manage any bookings',
            
            // Payment Management
            'process payments',
            'manage transactions',
            
            // Inspection & Handover
            'conduct inspections',
            'sign documents',
            
            // Communication
            'send messages',
            'manage notifications',
            
            // Review & Rating
            'manage reviews',
            'moderate reviews',
            
            // Damage & Dispute
            'report damage',
            'dispute damage',
            'resolve disputes',
            
            // Conflicts & Late Returns
            'manage conflicts',
            'approve compensations',
            
            // Collections
            'view own collections',
            'manage collections',
            'escalate collections',
            
            // Deposit Management
            'manage deposits',
            
            // User Management
            'verify users',
            'manage users',
            
            // Support Tickets
            'create tickets',
            'resolve tickets',
            
            // Reports & Analytics
            'view reports',
            'generate reports',
            
            // System Settings
            'manage settings',
            'configure system',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);
        $superadmin->givePermissionTo([
            'view dashboard',
            'manage any profile',
            'manage any vehicles',
            'verify vehicles',
            'manage any bookings',
            'manage transactions',
            'manage notifications',
            'moderate reviews',
            'resolve disputes',
            'manage conflicts',
            'approve compensations',
            'manage collections',
            'escalate collections',
            'manage deposits',
            'verify users',
            'manage users',
            'resolve tickets',
            'view reports',
            'generate reports',
            'manage settings',
            'configure system',
        ]);

        $client = Role::firstOrCreate(['name' => 'client']);
        $client->givePermissionTo([
            'view dashboard',
            'manage own profile',
            'manage own bookings',
            'process payments',
            'conduct inspections',
            'sign documents',
            'send messages',
            'manage reviews',
            'dispute damage',
            'view own collections',
            'create tickets',
        ]);

        $operator = Role::firstOrCreate(['name' => 'operator']);
        $operator->givePermissionTo([
            'view dashboard',
            'manage own profile',
            'manage own vehicles',
            'manage booking requests',
            'process payments',
            'conduct inspections',
            'sign documents',
            'send messages',
            'manage reviews',
            'report damage',
            'view own collections',
            'create tickets',
        ]);

        $this->command->info('Permissions and roles seeded successfully!');
    }
}