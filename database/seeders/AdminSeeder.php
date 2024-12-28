<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $admin_email = env('INIT_ADMIN_EMAIL', 'admin@example.com');

        if (!DB::table('admins')->where('email', $admin_email)->exists()) {
            Admin::create([
                'email' => $admin_email,
                'password' => env('INIT_ADMIN_PASSWORD', 'password'),
                'name' => 'init admin',
            ]);
        }
    }
}
