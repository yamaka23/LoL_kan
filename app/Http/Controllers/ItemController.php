<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    /**
     * Riot APIからアイテムデータを取得し、データベースを同期する
     */
    public function syncItems()
    {
        // 1. 最新バージョンを取得
        try {
            $versionsResponse = Http::withoutVerifying()->get('https://ddragon.leagueoflegends.com/api/versions.json');
            $latestVersion = $versionsResponse->json()[0];
        } catch (\Exception $e) {
            return response()->json(['error' => '最新バージョンの取得に失敗しました。', 'message' => $e->getMessage()], 500);
        }

        // 2. アイテムデータをAPIから取得
        $itemDataUrl = "https://ddragon.leagueoflegends.com/cdn/{$latestVersion}/data/ja_JP/item.json";
        try {
            $itemApiResponse = Http::withoutVerifying()->get($itemDataUrl)->json();
            $itemData = $itemApiResponse['data'] ?? [];
        } catch (\Exception $e) {
            return response()->json(['error' => 'アイテムデータの取得に失敗しました。', 'message' => $e->getMessage()], 500);
        }

        $savedCount = 0;

        DB::beginTransaction();
        try {
            // 3. 取得したアイテムデータをループして保存
            foreach ($itemData as $itemId => $details) {
                
                $tags = $details['tags'] ?? [];

                // このアイテムがブーツから作られるかどうかのフラグ
                $isBuiltFromBoot = false;
                if (isset($details['from'])) {
                    foreach ($details['from'] as $fromId) {
                        if (isset($itemData[$fromId]) && in_array('Boots', $itemData[$fromId]['tags'] ?? [])) {
                            $isBuiltFromBoot = true;
                            break;
                        }
                    }
                }

                // 条件1：このアイテムは「サモナーズリフトで使える、通常の派生ブーツ」か？
                $isUpgradedBoot = in_array('Boots', $tags)
                    && ($details['gold']['total'] > 300)
                    && isset($details['maps']['11']) && $details['maps']['11'] === true
                    && empty($details['requiredChampion']);

                // 条件2：このアイテムは「ブーツ以外の、サモナーズリフトで使える完成品」か？
                $isNormalItem = (
                    !in_array('Boots', $tags) &&
                    !$isBuiltFromBoot && // ★ブーツから作られる特殊アイテム（ガンメタルブーツなど）を除外★
                    !in_array('Consumable', $tags) &&
                    !in_array('Trinket', $tags) &&
                    !in_array('Jungle', $tags) &&
                    !in_array('Lane', $tags) &&
                    isset($details['gold']['purchasable']) && $details['gold']['purchasable'] &&
                    !isset($details['into']) &&
                    isset($details['maps']['11']) && $details['maps']['11'] === true &&
                    empty($details['requiredChampion']) &&
                    empty($details['requiredAlly']) &&
                    $itemId < 7000
                );
                
                // 「派生ブーツ」または「ブーツ以外の完成品」の場合のみDBに保存
                if ($isUpgradedBoot || $isNormalItem) {
                    Item::updateOrCreate(
                        ['id' => $itemId],
                        [
                            'name' => $details['name'],
                            'description' => strip_tags($details['description']),
                            'plaintext' => $details['plaintext'] ?? '',
                            'gold' => $details['gold']['total'],
                            'image' => $details['image']['full'],
                            'version' => $latestVersion,
                            'tags' => $tags,
                        ]
                    );
                    $savedCount++;
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('アイテム同期エラー: ' . $e->getMessage());
            return response()->json(['error' => 'DB保存中にエラーが発生しました。', 'message' => $e->getMessage()], 500);
        }

        return response()->json([
            'message' => 'アイテムデータの同期が完了しました。',
            'version' => $latestVersion,
            'saved_count' => $savedCount
        ]);
    }
}
