<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::updateOrCreate([
            'email' => 'admin1@admin.com',
        ], [
            'name' => 'Admin-One',
            'password' => Hash::make('pass123'),
        ]);

        Admin::updateOrCreate([
            'email' => 'admin2@admin.com',
        ], [
            'name' => 'Admin-Two',
            'password' => Hash::make('pass123'),
        ]);
    }
}
