<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1️⃣ Create Admin User
        $superadmin = User::create([
            'name' => 'Super Admin User',
            'email' => 'adminAko@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'superadmin'
        ]);
        $superadmin->assignRole('superadmin'); 

         // 3 Create client User
        $client = User::create([
            'name' => 'client101',
            'email' => 'client@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'client'
        ]);
        $client->assignRole('client'); 
            // 3 Create client User
        $operator = User::create([
            'name' => 'operator655',
            'email' => 'operator@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'operator'
        ]);
        $operator->assignRole('operator'); 
    }
}
