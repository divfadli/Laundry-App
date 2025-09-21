<?php

namespace Database\Seeders;

use App\Models\TypeOfServices;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeOfServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'service_name' => 'Cuci & Gosok',
                'price' => 5000,
                'description' => 'Layanan cuci dan gosok lengkap'
            ],
            [
                'service_name' => 'Hanya Cuci',
                'price' => 4500,
                'description' => 'Layanan cuci saja tanpa gosok'
            ],
            [
                'service_name' => 'Hanya Gosok',
                'price' => 5000,
                'description' => 'Layanan gosok saja'
            ],
            [
                'service_name' => 'Laundry Besar',
                'price' => 7000,
                'description' => 'Selimut, Karpet, Mantel, Sprei My Love'
            ]
        ];

        foreach ($services as $service) {
            TypeOfServices::create($service);
        }
    }
}
