<?php

namespace App\Http\Controllers;

use App\Models\Champion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ChampionController extends Controller
{
    /**
     * チャンピオンデータの同期処理
     */
    public function syncChampions()
    {
        $language = 'ja_JP'; // API用

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

        // 2. チャンピオンデータを取得
        $championDataUrl = "https://ddragon.leagueoflegends.com/cdn/{$latestVersion}/data/ja_JP/champion.json";
        try {
            $championsResponse = Http::withoutVerifying()->get($championDataUrl);
            $championsResponse->throw();

            $championListData = $championsResponse->json()['data'] ?? null;
            if (!$championListData) {
                return response()->json([
                    'error' => 'チャンピオンデータの取得に失敗しました。',
                    'message' => 'APIレスポンスに "data" キーが含まれていません。構造が想定と異なります。',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'チャンピオンデータの取得中にエラーが発生しました。',
                'message' => $e->getMessage(),
            ], 500);
        }

        // 3. データベースに保存
        $savedCount = 0;
        $errorCount = 0;

        DB::beginTransaction();
        try {
            foreach ($championListData as $championApiId => $details) {
                try {
                    Champion::updateOrCreate(
                        [
                            'id' => $details['id'], // API ID（文字列）をそのまま主キーとして使用
                            'language' => $language,
                        ],
                        [
                            'name' => $details['name'],
                            'image' => $details['image']['full'] ?? null,
                            'version' => $latestVersion,
                        ]
                    );
                    $savedCount++;
                } catch (\Exception $e) {
                    Log::error("チャンピオンの保存に失敗しました: {$details['id']}", ['message' => $e->getMessage()]);
                    $errorCount++;
                }
            }

            DB::commit();
            return response()->json([
                'message' => 'チャンピオンデータの同期が完了しました。',
                'version' => $latestVersion,
                'language' => $language,
                'saved_count' => $savedCount,
                'error_count' => $errorCount,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('チャンピオン同期中にトランザクションエラー', ['message' => $e->getMessage()]);
            return response()->json([
                'error' => '同期処理中にエラーが発生しました。',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * チャンピオン一覧の表示
     */
    public function listChampions()
    {
        $language = 'ja_JP'; 

        $latestVersionEntry = Champion::where('language', $language)
            ->orderBy('version', 'desc')
            ->first();

        if (!$latestVersionEntry) {
            return view('champions.list', [
                'champions' => collect(),
                'message' => '表示できるチャンピオンデータがありません。先にデータを同期してください。',
            ]);
        }

        $champions = Champion::where('language', $language)
            ->where('version', $latestVersionEntry->version)
            ->orderBy('name')
            ->get();

        return view('champions.list', [
            'champions' => $champions,
        ]);
    }
}
