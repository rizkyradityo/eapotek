<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Obat Bebas', 'description' => 'Obat yang dapat dibeli tanpa resep dokter'],
            ['name' => 'Obat Bebas Terbatas', 'description' => 'Obat bebas yang pembeliannya dibatasi'],
            ['name' => 'Obat Wajib Apotek', 'description' => 'Obat yang hanya dapat dibeli di apotek'],
            ['name' => 'Obat Keras', 'description' => 'Obat yang memerlukan resep dokter'],
            ['name' => 'Suplemen', 'description' => 'Suplemen nutrisi dan vitamin'],
            ['name' => 'Alat Kesehatan', 'description' => 'Alat-alat kesehatan'],
            ['name' => 'Produk Perawatan', 'description' => 'Produk perawatan tubuh dan kecantikan'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
