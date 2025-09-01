<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LaneSeeder::class,
            UserSeeder::class,
            PostSeeder::class,
            StatRuneSeeder::class,
            // 他のシーダーもここに追加できます



        ]);
        
    }
}
