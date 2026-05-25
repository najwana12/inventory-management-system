<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate([
            "name" => "pimpinan"
        ]);

        Role::firstOrCreate([
            "name" => "super_admin"
        ]);

        Role::firstOrCreate([
            "name" => "admin"
        ]);

        Role::firstOrCreate([
            "name" => "staff"
        ]);

        // hanya lihat report
        Role::firstOrCreate([
            "name" => "sales"
        ]);

        // hanya lihat report
        Role::firstOrCreate([
            "name" => "pembelian"
        ]);
    }
}