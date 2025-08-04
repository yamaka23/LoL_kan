<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StatRune;

class StatRuneSeeder extends Seeder
{
    public function run(): void
    {
        $statRunes = [
            // Tier 0 (Offense)
            ['api_id' => 5008, 'name' => 'アダプティブ', 'icon_path' => 'https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/perk-images/statmods/statmodsadaptiveforceicon.png', 'tier' => 0, 'slot_index' => 0],
            ['api_id' => 5005, 'name' => '攻撃速度', 'icon_path' => 'https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/perk-images/statmods/statmodsattackspeedicon.png', 'tier' => 0, 'slot_index' => 1],
            ['api_id' => 5007, 'name' => 'スキルヘイスト', 'icon_path' => 'https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/perk-images/statmods/statmodscdrscalingicon.png', 'tier' => 0, 'slot_index' => 2],
            // Tier 1 (Flex)
            ['api_id' => 5008, 'name' => 'アダプティブ', 'icon_path' => 'https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/perk-images/statmods/statmodsadaptiveforceicon.png', 'tier' => 1, 'slot_index' => 0],
            ['api_id' => 5002, 'name' => '物理防御', 'icon_path' => 'https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/perk-images/statmods/statmodsarmoricon.png', 'tier' => 1, 'slot_index' => 1],
            ['api_id' => 5003, 'name' => '魔法防御', 'icon_path' => 'https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/perk-images/statmods/statmodsmagicresicon.png', 'tier' => 1, 'slot_index' => 2],
            // Tier 2 (Defense)
            ['api_id' => 5001, 'name' => '体力', 'icon_path' => 'https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/perk-images/statmods/statmodshealthscalingicon.png', 'tier' => 2, 'slot_index' => 0],
            ['api_id' => 5010, 'name' => '行動妨害耐性', 'icon_path' => 'https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/perk-images/statmods/statmodstenacityicon.png', 'tier' => 2, 'slot_index' => 1],
            ['api_id' => 5011, 'name' => '移動速度', 'icon_path' => 'https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/perk-images/statmods/statmodsmovementspeedicon.png', 'tier' => 2, 'slot_index' => 2],
        ];

        foreach ($statRunes as $rune) {
            // 'api_id' と 'tier' の組み合わせでデータを探し、なければ作成、あれば更新します
            StatRune::updateOrCreate(
                ['api_id' => $rune['api_id'], 'tier' => $rune['tier']],
                $rune
            );
        }
    }
}
