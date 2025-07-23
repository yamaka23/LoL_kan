<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    /**
     * アイテムデータの同期処理
     */
    public function syncItems()
    {
        $language = 'ja';

        // 1. 最新バージョンを取得
        try {
            $versionsResponse = Http::withoutVerifying()->get('https://ddragon.leagueoflegends.com/api/versions.json');
            $versionsResponse->throw();

            $latestVersion = $versionsResponse->json()[0] ?? null;
            if (!$latestVersion) {
                return response()->json([
                    'error' => '最新バージョンの取得に失敗しました。',
                    'message' => 'バージョン情報がAPIレスポンスに含まれていません。',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'バージョン一覧の取得中にエラーが発生しました。',
                'message' => $e->getMessage(),
            ], 500);
        }

        // 2. アイテムデータを取得
        $itemsDataUrl = "https://ddragon.leagueoflegends.com/cdn/{$latestVersion}/data/ja_JP/item.json";
        try {
            $itemsResponse = Http::withoutVerifying()->get($itemsDataUrl);
            $itemsResponse->throw();

            $itemListData = $itemsResponse->json()['data'] ?? null;
            if (!$itemListData) {
                return response()->json([
                    'error' => 'アイテムデータの取得に失敗しました。',
                    'message' => 'APIレスポンスに "data" キーが含まれていません。構造が予期された形式と異なります。',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'アイテムデータの取得中にエラーが発生しました。',
                'message' => $e->getMessage(),
            ], 500);
        }

        // 3. データベースに保存
        $savedCount = 0;
        $errorCount = 0;

        DB::beginTransaction();
        try {
            foreach ($itemListData as $itemApiId => $details) {
                try {
                    Item::updateOrCreate(
                        [
                            'id' => $itemApiId,
                            'language' => $language,
                        ],
                        [
                            'name' => $details['name'] ?? '',
                            'image' => $details['image']['full'] ?? null,
                            'stats' => $details['stats'] ?? [],
                            'description' => strip_tags($details['description'] ?? ''),
                            'plaintext' => $details['plaintext'] ?? null,
                            'gold' => $details['gold'] ?? [],
                            'tags' => $details['tags'] ?? [],
                            'version' => $latestVersion,
                            'in_store' => $details['inStore'] ?? true,
                            'purchasable' => $details['gold']['purchasable'] ?? true,
                            'required_champion' => $details['requiredChampion'] ?? null,
                            'depth' => $details['depth'] ?? null,
                            'is_summoners_rift_available' => $details['maps']['11'] ?? false,
                            'colloq' => $details['colloq'] ?? null,
                        ]
                    );
                    $savedCount++;
                } catch (\Exception $e) {
                    Log::error("アイテムの保存に失敗しました: {$itemApiId}", ['message' => $e->getMessage()]);
                    $errorCount++;
                }
            }

            DB::commit();
            return response()->json([
                'message' => 'アイテムデータの同期が完了しました。',
                'version' => $latestVersion,
                'language' => $language,
                'saved_count' => $savedCount,
                'error_count' => $errorCount,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('アイテム同期中にエラーが発生しました。', ['message' => $e->getMessage()]);
            return response()->json([
                'error' => '同期処理中にエラーが発生しました。',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * アイテム一覧を表示
     */
    public function listItems()
    {
        $language = 'ja';

        $latestVersionEntry = Item::where('language', $language)
            ->orderBy('version', 'desc')
            ->first();

        if (!$latestVersionEntry) {
            return view('items.list', [
                'items' => collect(),
                'message' => '表示できるアイテムデータがDBにありません。先にデータを同期してください。',
            ]);
        }

        $items = Item::where('language', $language)
            ->where('version', $latestVersionEntry->version)
            ->orderBy('name')
            ->get();

        return view('items.list', [
            'items' => $items,
        ]);
    }
}
