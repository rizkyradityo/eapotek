<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Tablet', 'symbol' => 'tab', 'description' => 'Tablet oral'],
            ['name' => 'Kapsul', 'symbol' => 'kps', 'description' => 'Kapsul oral'],
            ['name' => 'Strip', 'symbol' => 'strip', 'description' => 'Strip berisi tablet/kapsul'],
            ['name' => 'Box', 'symbol' => 'box', 'description' => 'Box berisi strip'],
            ['name' => 'Botol', 'symbol' => 'botol', 'description' => 'Botol cairan'],
            ['name' => 'Sachet', 'symbol' => 'sachet', 'description' => 'Sachet serbuk/cairan'],
            ['name' => 'Tube', 'symbol' => 'tube', 'description' => 'Tabung salep/krim'],
            ['name' => 'Ampul', 'symbol' => 'ampul', 'description' => 'Ampul injeksi'],
            ['name' => 'Vial', 'symbol' => 'vial', 'description' => 'Vial injeksi'],
            ['name' => 'Pcs', 'symbol' => 'pcs', 'description' => 'Pieces (keping)'],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
