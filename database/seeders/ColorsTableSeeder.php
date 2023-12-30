<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Color;

class ColorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colorsData = '
        [
            {
                "id": 1,
                "name": "Block",
                "nameMn": null
            },
            {
                "id": 2,
                "name": "Red",
                "nameMn": null
            },
            {
                "id": 3,
                "name": "Blue",
                "nameMn": null
            },
            {
                "id": 4,
                "name": "Gray",
                "nameMn": null
            },
            {
                "id": 5,
                "name": "Green",
                "nameMn": null
            },
            {
                "id": 6,
                "name": "Black",
                "nameMn": null
            }
        ]
        ';

        // Convert JSON string to PHP array
        $dataArray = json_decode($colorsData, true);

        // Insert data into the database
        foreach ($dataArray as $data) {
            DB::table('colors')->insert($data);
        }
    }
}
