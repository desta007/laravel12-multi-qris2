<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password' => bcrypt('password')
        ]);
        $admin->assignRole('super_admin');

        $staff = User::create([
            'name' => 'Staff Admin',
            'email' => 'staff@admin.com',
            'password' => bcrypt('password')
        ]);
        $staff->assignRole('staff_admin');

        $member = User::create([
            'name' => 'Member User',
            'email' => 'member@user.com',
            'password' => bcrypt('password')
        ]);
        $member->assignRole('member');
    }
}
