<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
   
    public function run(): void
    {
        $email = 'superadmin@gmail.com';

        $exists = DB::selectOne('SELECT id FROM users WHERE email = ?', [$email]);

        if ($exists) {
            return;
        }

        $now = now()->toDateTimeString();

        DB::insert(
            'INSERT INTO users (company_id, name, email, role, password, email_verified_at, created_at, updated_at)
             VALUES (NULL, ?, ?, ?, ?, ?, ?, ?)',
            ['Super Admin', $email, 'super_admin', Hash::make('password'), $now, $now, $now]
        );
    }
}
