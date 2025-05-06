<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Region;
use App\Models\Wilayah;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jakarta = Wilayah::where('name', 'Jakarta')->first();
        $bdg = Wilayah::where('name', 'Bandung')->first();
        $region1 = $jakarta->region;
        $region2 = $bdg->region;
        
        User::create([
            'name' => 'Ihya',
            'email' => 'ihya@example.com',
            'password' => Hash::make('password'),
            'region_id' => $region1->id,
            'wilayah_id' => $jakarta->id,
        ]);

        User::create([
            'name' => 'botfd',
            'email' => 'bot@example.com',
            'password' => Hash::make('password'),
            'region_id' => $region1->id,
            'wilayah_id' => $jakarta->id,
        ]);
        User::create([
            'name' => 'pusdatin',
            'email' => 'pusdatin@gmail.com',
            'password' => Hash::make('password'),
            'region_id' => $region2->id,
            'wilayah_id' => $bdg->id,
        ]);

    }
}
