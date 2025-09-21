<?php

namespace Database\Seeders;

use App\Models\Customers;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $customers = [
            [
                'customer_name' => 'John Doe',
                'phone' => '081234567890',
                'address' => 'Jl. Contoh No. 123, Jakarta'
            ],
            [
                'customer_name' => 'Jane Smith',
                'phone' => '081987654321',
                'address' => 'Jl. Sample No. 456, Bandung'
            ]
        ];

        foreach ($customers as $customer) {
            Customers::create($customer);
        }
    }
}
