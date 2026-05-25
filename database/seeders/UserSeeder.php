<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $role_pimpinan    = Role::where('name','pimpinan')->first();
        $role_super_admin = Role::where('name','super_admin')->first();
        $role_admin       = Role::where('name','admin')->first();
        $role_staff       = Role::where('name','staff')->first();
        $role_sales       = Role::where('name','sales')->first();
        $role_pembelian   = Role::where('name','pembelian')->first();

        User::firstOrCreate([
            "username" => "pimpinan"
        ],[
            "name" => "Pimpinan",
            "role_id" => $role_pimpinan->id,
            "password" => bcrypt('12345678')
        ]);

        User::firstOrCreate([
            "username" => "superadmin"
        ],[
            "name" => "Super Admin",
            "role_id" => $role_super_admin->id,
            "password" => bcrypt('12345678')
        ]);

        User::firstOrCreate([
            "username" => "admin"
        ],[
            "name" => "Admin",
            "role_id" => $role_admin->id,
            "password" => bcrypt('12345678')
        ]);

        User::firstOrCreate([
            "username" => "staff"
        ],[
            "name" => "Staff",
            "role_id" => $role_staff->id,
            "password" => bcrypt('12345678')
        ]);

        User::firstOrCreate([
            "username" => "sales"
        ],[
            "name" => "Sales",
            "role_id" => $role_sales->id,
            "password" => bcrypt('12345678')
        ]);

        User::firstOrCreate([
            "username" => "pembelian"
        ],[
            "name" => "Pembelian",
            "role_id" => $role_pembelian->id,
            "password" => bcrypt('12345678')
        ]);
    }
}