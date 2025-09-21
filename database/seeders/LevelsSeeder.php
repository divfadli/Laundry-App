<?php

namespace Database\Seeders;

use App\Models\Levels;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LevelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            ['level_name' => 'Administrator'],
            ['level_name' => 'Operator'],
            ['level_name' => 'Pimpinan'],
        ];

        foreach( $levels as $level ) {
            Levels::create($level);
        }
    }
}
