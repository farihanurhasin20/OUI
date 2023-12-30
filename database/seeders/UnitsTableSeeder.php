<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Unit;

class UnitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonData = '
        [
            {
                "id": 1,
                "name": "Amount",
                "nameMn": null
            },
            {
                "id": 2,
                "name": "Bottle",
                "nameMn": null
            },
            {
                "id": 3,
                "name": "Box",
                "nameMn": null
            },
            {
                "id": 4,
                "name": "Feet",
                "nameMn": null
            },
            {
                "id": 5,
                "name": "Kg",
                "nameMn": null
            },
            {
                "id": 6,
                "name": "Km",
                "nameMn": null
            },
            {
                "id": 7,
                "name": "Ltr",
                "nameMn": null
            },
            {
                "id": 8,
                "name": "Meter",
                "nameMn": null
            },
            {
                "id": 9,
                "name": "Tk",
                "nameMn": null
            }
        ]
        ';

        // Convert JSON string to PHP array
        $dataArray = json_decode($jsonData, true);

        // Insert data into the database
        foreach ($dataArray as $data) {
            $data['created_at'] = now();
            $data['updated_at'] = now();
            DB::table('units')->insert($data);
        }
    }
}
