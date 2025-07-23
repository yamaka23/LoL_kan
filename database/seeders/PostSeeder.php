<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;

class PostSeeder extends Seeder
{

    public function run(): void
    {
        Post::create([
            'user_id' => 1,
            'champion_id' => 'Urgot',
            'vs_champion_id' => 'Aatrox',
            'lane_id' => 1,
            'title' => 'アーゴットでエイトロに勝つ方法',
            'content' => '耐久重視でWから仕掛けよう',
        ]);
    }
}
