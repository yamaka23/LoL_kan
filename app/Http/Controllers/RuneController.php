<?php

namespace App\Http\Controllers;

use App\Models\Rune;
use App\Models\RunePath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RuneController extends Controller
{
    public function syncRunes()
    {
        try {
            $versionsResponse = Http::withoutVerifying()->get('https://ddragon.leagueoflegends.com/api/versions.json');
            $latestVersion = $versionsResponse->json()[0];

            $runeDataUrl = "https://ddragon.leagueoflegends.com/cdn/{$latestVersion}/data/ja_JP/runesReforged.json";
            $runePathsData = Http::withoutVerifying()->get($runeDataUrl)->json();
        } catch (\Exception $e) {
            return response()->json(['error' => 'APIからのデータ取得に失敗しました。', 'message' => $e->getMessage()], 500);
        }

        DB::beginTransaction();
        try {
            foreach ($runePathsData as $pathData) {
                RunePath::updateOrCreate(
                    ['id' => $pathData['id']],
                    [
                        'key' => $pathData['key'],
                        'name' => $pathData['name'],
                        'icon_path' => 'https://ddragon.leagueoflegends.com/cdn/img/' . $pathData['icon'],
                    ]
                );
                foreach ($pathData['slots'] as $tier => $slotData) {
                    foreach ($slotData['runes'] as $slotIndex => $runeData) {
                        Rune::updateOrCreate(
                            ['id' => $runeData['id']],
                            [
                                'name' => $runeData['name'],
                                'rune_path_id' => $pathData['id'],
                                'tier' => $tier,
                                'slot_index' => $slotIndex,
                                'icon_path' => 'https://ddragon.leagueoflegends.com/cdn/img/' . $runeData['icon'],
                                'longDesc' => strip_tags($runeData['longDesc']),
                            ]
                        );
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ルーン同期エラー: ' . $e->getMessage());
            return response()->json(['error' => 'DB保存中にエラーが発生しました。', 'message' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'メインルーンの同期が完了しました。']);
    }
}
