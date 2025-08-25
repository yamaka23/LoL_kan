<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StatRune;

class StatRuneSeeder extends Seeder
{
    public function run(): void
    {
        // データベースを一度空にする
        StatRune::truncate();

        $statRunes = [
            // Tier 0 (Offense)
            ['api_id' => 5008, 'name' => 'アダプティブ', 'icon_path' => 'https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/perk-images/statmods/statmodsadaptiveforceicon.png', 'tier' => 0, 'slot_index' => 0],
            ['api_id' => 5005, 'name' => '攻撃速度', 'icon_path' => 'https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/perk-images/statmods/statmodsattackspeedicon.png', 'tier' => 0, 'slot_index' => 1],
            ['api_id' => 5007, 'name' => 'スキルヘイスト', 'icon_path' => 'https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/perk-images/statmods/statmodscdrscalingicon.png', 'tier' => 0, 'slot_index' => 2],
            
            // Tier 1 (Flex)
            ['api_id' => 5008, 'name' => 'アダプティブ', 'icon_path' => 'https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/perk-images/statmods/statmodsadaptiveforceicon.png', 'tier' => 1, 'slot_index' => 0],
            ['api_id' => 5011, 'name' => '移動速度', 'icon_path' => 'https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/perk-images/statmods/statmodsmovementspeedicon.png', 'tier' => 1, 'slot_index' => 1],
            ['api_id' => 5001, 'name' => 'スケーリング体力', 'icon_path' => 'https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/perk-images/statmods/statmodshealthplusicon.png', 'tier' => 1, 'slot_index' => 2], // URLを修正
            
            // Tier 2 (Defense)
            ['api_id' => 5013, 'name' => '体力', 'icon_path' => 'https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/perk-images/statmods/statmodshealthscalingicon.png', 'tier' => 2, 'slot_index' => 0],
            ['api_id' => 5010, 'name' => '行動妨害耐性', 'icon_path' => 'https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/perk-images/statmods/statmodstenacityicon.png', 'tier' => 2, 'slot_index' => 1],
            ['api_id' => 5001, 'name' => 'スケーリング体力', 'icon_path' => 'https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/perk-images/statmods/statmodshealthplusicon.png', 'tier' => 2, 'slot_index' => 2], // URLを修正
        ];

        foreach ($statRunes as $rune) {
            StatRune::updateOrCreate(
                ['api_id' => $rune['api_id'], 'tier' => $rune['tier']],
                $rune
            );
        }
    }
}
