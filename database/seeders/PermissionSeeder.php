<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Admin;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'can_accept_topup',
            'can_reject_topup',
            'can_accept_withdrawals',
            'can_reject_withdrawals',
        ];

        // تأكد من وجود كل الـ permissions
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Admin 1 => كل الصلاحيات
        $admin1 = Admin::where('email', 'admin1@admin.com')->first();
        if ($admin1) {
            $admin1->permissions()->sync(Permission::pluck('id')->toArray());
        }

        // Admin 2 => سحب فقط
        $admin2 = Admin::where('email', 'admin2@admin.com')->first();
        if ($admin2) {
            $withdrawPermissions = Permission::whereIn('name', [
                'can_accept_withdrawals',
                'can_reject_withdrawals',
            ])->pluck('id')->toArray();

            $admin2->permissions()->sync($withdrawPermissions);
        }
    }
}
