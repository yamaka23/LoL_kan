<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->updateOrInsert( // ユーザーテーブルを初期化

            [
                'name' => 'TestUser',
                'password' => Hash::make('password'), // パスワードはハッシュ化
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
