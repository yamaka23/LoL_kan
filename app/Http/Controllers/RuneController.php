<?php

namespace App\Http\Controllers;

use App\Models\Rune;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\MainRune;

class RuneController extends Controller
{
    /**
     * ルーンデータの同期処理
     */
    public function syncRunes()
    {
        $languageApi = 'ja_JP'; // API用
        $language = 'ja';       // DB保存用


        // 1. 最新バージョンを取得 
        try {
            $versionsResponse = Http::withoutVerifying()->get('https://ddragon.leagueoflegends.com/api/versions.json');
            $versionsResponse->throw();
            $latestVersion = $versionsResponse->json()[0] ?? null;
            if (!$latestVersion) { 
                return response()->json([
                    'error' => '最新バージョンの取得に失敗しました。',
                    'message' => 'APIレスポンスにバージョン情報が含まれていません。',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'バージョン一覧の取得中にエラーが発生しました。',
                'message' => $e->getMessage(),
            ], 500);
        }


        // 2. ルーンデータを取得 
        $runeDataUrl = "https://ddragon.leagueoflegends.com/cdn/{$latestVersion}/data/{$language}/runesReforged.json";
        try {
            $runesResponse = Http::withoutVerifying()->get($runeDataUrl);
            $runesResponse->throw();
            $runePathsData = $runesResponse->json();
            if (empty($runePathsData)) {
                return response()->json([
                    'error' => 'ルーンデータの取得に失敗しました。',
                    'message' => 'APIレスポンスにルーンパス情報が含まれていません。',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'ルーンデータの取得中にエラーが発生しました。',
                'message' => $e->getMessage(),
            ], 500);
        }


        // 3. データベースに保存
        $savedMainRunes = 0;
        $savedNormalRunes = 0;
        $errorCount = 0;

        DB::beginTransaction();
        try {
            // ルーンパスのループ (覇道, 栄華, ...)
            foreach ($runePathsData as $pathData) {
                try {
                    // ▼▼▼ ここを変更 ▼▼▼
                    // 1. パス情報を `main_runes` テーブルに保存
                    $mainRune = MainRune::updateOrCreate(
                        ['id' => $pathData['id']],
                        [
                            'name' => $pathData['name'],
                            'icon' => $pathData['icon'],
                        ]
                    );
                    $savedMainRunes++;

                    // スロットのループ
                    foreach ($pathData['slots'] as $slotIndex => $slotData) {
                        // 個別ルーンのループ
                        foreach ($slotData['runes'] as $runeData) {
                            
                            // ▼▼▼ ここを変更 ▼▼▼
                            // 2. 個別ルーン情報をすべて `runes` テーブルに保存
                            Rune::updateOrCreate(
                                ['id' => $runeData['id']],
                                [
                                    // 親であるパスのIDと紐づけ
                                    'main_rune_id' => $pathData['id'], 
                                    'name' => $runeData['name'],
                                    'icon' => $runeData['icon'],
                                    'long_desc' => $runeData['longDesc'] ?? '',
                                    'slot_index' => $slotIndex, // スロットのインデックスを保存

                                ]
                            );
                            $savedNormalRunes++;
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("RuneSync: Path ID {$pathData['id']} の保存に失敗しました。", ['message' => $e->getMessage()]);
                    $errorCount++;
                }
            }

            DB::commit();
            return response()->json([
                'message' => 'ルーンデータの同期が完了しました。',
                'version' => $latestVersion,
                'saved_main_runes' => $savedMainRunes,
                'saved_normal_runes' => $savedNormalRunes,
                'error_count' => $errorCount,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ルーン同期処理中に致命的なエラーが発生しました。', ['message' => $e->getMessage()]);
            return response()->json([
                'error' => '同期処理中に致命的なエラーが発生しました。',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ルーン一覧の表示
     */
    public function listRunes()
    {
        $language = 'ja_JP';
        $latestVersionEntry = Rune::where('language', $language)
                                    ->orderBy('version', 'desc')
                                    ->first();
                                
        if (!$latestVersionEntry) {
            return view('runes.list', [
                'runes' => collect(),
                'message' => '表示できるルーンデータがDBにありません。先にデータを同期してください。',
            ]);
        }

        $runes = Rune::where('language', $language)
                        ->where('version', $latestVersionEntry->version)
                        ->orderBy('name')
                        ->get();


        return view('runes.list', [
            'runes' => $runes,
        ]);
    }
}
