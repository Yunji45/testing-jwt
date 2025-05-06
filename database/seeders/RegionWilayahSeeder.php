<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Region;
use App\Models\Wilayah;

class RegionWilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            'Region 1',
            'Region 2',
            'Region 3',
            'Region 4',
            'Region 5',
        ];

        $wilayahList = [
            'Jakarta',
            'Bandung',
            'Surabaya',
            'Purwokerto',
            'Semarang',
        ];

        foreach ($regions as $index => $regionName) {
            $region = Region::create(['name' => $regionName]);

            // Setiap region punya 1 wilayah untuk awal
            Wilayah::create([
                'name' => $wilayahList[$index],
                'region_id' => $region->id,
            ]);
        }
    }
}
