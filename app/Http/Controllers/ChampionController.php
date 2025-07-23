<?php

namespace App\Http\Controllers;

use App\Models\Champion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ChampionController extends Controller
{
    public function syncChampions()
    {
        $language = 'ja_JP';

        // 1. 最新バージョンを取得 (変更なし)
        try {
            $versionsResponse = Http::withoutVerifying()->get('https://ddragon.leagueoflegends.com/api/versions.json');
            $versionsResponse->throw();
            $latestVersion = $versionsResponse->json()[0] ?? null;
            if (!$latestVersion) { /* ...エラー処理... */ return response()->json(['error' => 'Could not determine the latest version.'], 500); }
        } catch (\Exception $e) { /* ...エラー処理... */ return response()->json(['error' => 'Failed to fetch latest version list.', 'message' => $e->getMessage()], 500); }

        // 2. チャンピオンデータを取得 (変更なし)
        $championDataUrl = "https://ddragon.leagueoflegends.com/cdn/{$latestVersion}/data/{$language}/champion.json";
        try {
            $championsResponse = Http::withoutVerifying()->get($championDataUrl);
            $championsResponse->throw();
            $championListData = $championsResponse->json()['data'] ?? null;
            if (!$championListData) { /* ...エラー処理... */ return response()->json(['error' => 'Champion data is not in the expected format.'], 500); }
        } catch (\Exception $e) { /* ...エラー処理... */ return response()->json(['error' => 'Failed to fetch champion data.', 'message' => $e->getMessage()], 500); }

        // 3. データベースに保存
        $savedCount = 0;
        $errorCount = 0;
        DB::beginTransaction();
        try {
            // APIレスポンスのトップレベルキーは $championApiId (例: "Aatrox")
            // $details['id'] は $championApiId と同じ
            foreach ($championListData as $championApiId => $details) {
                try {
                    Champion::updateOrCreate(
                        [
                            // DBの 'id' カラム (主キー) に APIの 'id' (文字列) を使う
                            'id' => $details['id'], // または $championApiId
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
                    Log::error("Failed to save champion: {$details['id']}", ['message' => $e->getMessage()]);
                    $errorCount++;
                }
            }
            DB::commit();
            return response()->json([
                'message' => 'Champions data synced successfully (using API string id as primary key)!',
                'version' => $latestVersion, 'language' => $language,
                'saved_count' => $savedCount, 'error_count' => $errorCount,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error during champion data sync: ' . $e->getMessage());
            return response()->json(['error' => 'Error during synchronization.', 'message' => $e->getMessage()], 500);
        }
    }

    // listChampions メソッドは変更なし
    public function listChampions()
    {
        $language = 'ja_JP';
        $latestVersionEntry = Champion::where('language', $language)
                                  ->orderBy('version', 'desc')
                                  ->first();

        if (!$latestVersionEntry) {
            return view('champions.list', ['champions' => collect(), 'message' => '表示できるチャンピオンデータがDBにありません。先にデータを同期してください。']);
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