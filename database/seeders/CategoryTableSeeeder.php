<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

class CategoryTableSeeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorydata = '
        [
            {
                "id": 1,
                "name": "Tacos",
                "slug": "tacos"
            },
            {
                "id": 2,
                "name": "Pani Puri",
                "slug": "pani-puri"
            },
            {
                "id": 3,
                "name": "Gyros",
                "slug": "gyros"
            },
            {
                "id": 4,
                "name": "Banh Mi",
                "slug": "banh-mi"
            },
            {
                "id": 5,
                "name": "Samosas",
                "slug": "samosas"
            },
            {
                "id": 6,
                "name": "Pizza",
                "slug": "pizza"
            }
        ]
        
        ';

        // Convert JSON string to PHP array
        $dataArray = json_decode($categorydata, true);

        // Insert data into the database
        foreach ($dataArray as $data) {
            $data['created_at'] = now();
            $data['updated_at'] = now();
        
            DB::table('categories')->insert($data);
        }
        
    
    }
}
