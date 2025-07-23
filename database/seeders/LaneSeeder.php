<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lanes = ['TOP', 'JG', 'MID', 'BOT', 'SUP'];

        foreach ($lanes as $lane) {
            DB::table('lanes')->updateOrInsert(
                ['name' => $lane], // 一意キー
                [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }
    }
}
