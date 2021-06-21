<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            [
                'name' => 'super',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'admin',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'branch_manager',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'manager',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'seller',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'customer',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'supplier',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        DB::table('user_role')->insert([
            [
                'user_id' => 1,
                'role_id' => 1,
                'created_at' => now(),
            ]
        ]);
    }
}
