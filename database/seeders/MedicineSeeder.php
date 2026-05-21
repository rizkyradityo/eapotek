<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use Carbon\Carbon;

class MedicineSeeder extends Seeder
{
    public function run(): void
    {
        $medicines = [
            [
                'code' => 'MED000001',
                'name' => 'Paracetamol 500mg',
                'category_id' => 1,
                'unit_id' => 1,
                'generic_name' => 'Paracetamol',
                'manufacturer' => 'PT. Kimia Farma',
                'price' => 2000,
                'min_stock' => 50,
                'description' => 'Obat penurun demam dan pereda nyeri',
                'composition' => 'Paracetamol 500mg',
                'batches' => [
                    ['batch_number' => 'PAR2401', 'expired_date' => '2025-12-31', 'quantity' => 100, 'purchase_price' => 1500, 'selling_price' => 2000],
                    ['batch_number' => 'PAR2402', 'expired_date' => '2026-03-15', 'quantity' => 80, 'purchase_price' => 1500, 'selling_price' => 2000],
                ],
            ],
            [
                'code' => 'MED000002',
                'name' => 'Amoxicillin 500mg',
                'category_id' => 4,
                'unit_id' => 1,
                'generic_name' => 'Amoxicillin',
                'manufacturer' => 'PT. Sanbe Farma',
                'price' => 5000,
                'min_stock' => 30,
                'description' => 'Antibiotik untuk infeksi bakteri',
                'composition' => 'Amoxicillin 500mg',
                'batches' => [
                    ['batch_number' => 'AMO2401', 'expired_date' => '2025-06-30', 'quantity' => 50, 'purchase_price' => 3500, 'selling_price' => 5000],
                ],
            ],
            [
                'code' => 'MED000003',
                'name' => 'Vitamin C 1000mg',
                'category_id' => 5,
                'unit_id' => 2,
                'generic_name' => 'Ascorbic Acid',
                'manufacturer' => 'PT. Dexa Medica',
                'price' => 3000,
                'min_stock' => 40,
                'description' => 'Suplemen vitamin C',
                'composition' => 'Vitamin C 1000mg',
                'batches' => [
                    ['batch_number' => 'VIT2401', 'expired_date' => '2026-01-15', 'quantity' => 120, 'purchase_price' => 2000, 'selling_price' => 3000],
                    ['batch_number' => 'VIT2402', 'expired_date' => '2026-05-20', 'quantity' => 100, 'purchase_price' => 2000, 'selling_price' => 3000],
                ],
            ],
            [
                'code' => 'MED000004',
                'name' => 'Omeprazole 20mg',
                'category_id' => 4,
                'unit_id' => 1,
                'generic_name' => 'Omeprazole',
                'manufacturer' => 'PT. Interbat',
                'price' => 4000,
                'min_stock' => 25,
                'description' => 'Obat maag dan lambung',
                'composition' => 'Omeprazole 20mg',
                'batches' => [
                    ['batch_number' => 'OMP2401', 'expired_date' => '2025-08-30', 'quantity' => 60, 'purchase_price' => 2800, 'selling_price' => 4000],
                ],
            ],
            [
                'code' => 'MED000005',
                'name' => 'Ibuprofen 400mg',
                'category_id' => 2,
                'unit_id' => 1,
                'generic_name' => 'Ibuprofen',
                'manufacturer' => 'PT. Ferron Par Pharmaceuticals',
                'price' => 2500,
                'min_stock' => 35,
                'description' => 'Obat anti nyeri dan anti inflamasi',
                'composition' => 'Ibuprofen 400mg',
                'batches' => [
                    ['batch_number' => 'IBU2401', 'expired_date' => '2026-02-28', 'quantity' => 70, 'purchase_price' => 1800, 'selling_price' => 2500],
                ],
            ],
        ];

        foreach ($medicines as $medicineData) {
            $batches = $medicineData['batches'];
            unset($medicineData['batches']);
            
            $medicine = Medicine::create($medicineData);
            
            foreach ($batches as $batchData) {
                MedicineBatch::create(array_merge($batchData, [
                    'medicine_id' => $medicine->id,
                ]));
            }
        }
    }
}
