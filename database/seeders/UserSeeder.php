<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Ihya Natik',
            'email' => 'ihya@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('contoh'), // pastikan dienkripsi
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name' => 'cahcah',
            'email' => 'cahcah@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('contoh'), // pastikan dienkripsi
            'created_at' => now(),
            'updated_at' => now(),
        ]);


    }
}
