<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => '管理者',
            // 'email' は削除済みなので使わない
            // 'email_verified_at' も不要
            'password' => Hash::make('sssggg08051023'), // 必要なら適宜変更
            // 'remember_token' も不要なら削除
        ]);
    }
}
