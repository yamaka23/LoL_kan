<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Champion;
use App\Models\Lane;
use App\Models\Rune;
use App\Models\RunePath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    /**
     * 投稿一覧
     */
    public function index()
    {
        $posts = Post::with(['user', 'champion'])->latest()->paginate(10);
        return view('posts.index', compact('posts'));
    }

    /**
     * チャンピオンごとの投稿一覧
     */
    public function championIndex(Champion $champion)
    {
        $posts = Post::where('champion_id', $champion->id)
            ->with(['lane', 'user'])
            ->latest()
            ->paginate(10);

        return view('posts.index', compact('posts', 'champion'));
    }

    /**
     * 投稿詳細
     */
    public function show(Post $post)
    {
        // 投稿に関連するデータをすべて読み込む
        $post->load([
            'user', 
            'champion', 
            'vsChampion', 
            'lane', 
            'runes.runePath', 
            'statRunes', 
            'items'
        ]);

        // ルーンをメインパスとサブパスに振り分ける
        $mainPathRunes = $post->runes->groupBy('rune_path_id')->first();
        $subPathRunes = $post->runes->groupBy('rune_path_id')->last();
        
        // メインパスとサブルーンの数が同じ場合は、サブパスが存在しないと見なす
        if ($mainPathRunes->count() === $subPathRunes->count()) {
            $subPathRunes = collect(); // 空のコレクション
        }

        // アイテムを order 順に並び替える
        $sortedItems = $post->items->sortBy('pivot.order');

        return view('posts.show', compact('post', 'mainPathRunes', 'subPathRunes', 'sortedItems'));
    }

    /**
     * 投稿作成画面
     */
    public function create()
    {
        // フォームの基本情報を取得
        $champions = \App\Models\Champion::orderBy('name')->get();
        $lanes = \App\Models\Lane::all();
        $runePaths = \App\Models\RunePath::all();
        $allRunes = \App\Models\Rune::all();
        $items = \App\Models\Item::all();

        

        $runesByPath = [];
        foreach ($runePaths as $path) {
            $runesByPath[$path->id] = [
                $allRunes->where('rune_path_id', $path->id)->where('tier', 0)->sortBy('slot_index')->values(),
                $allRunes->where('rune_path_id', $path->id)->where('tier', 1)->sortBy('slot_index')->values(),
                $allRunes->where('rune_path_id', $path->id)->where('tier', 2)->sortBy('slot_index')->values(),
                $allRunes->where('rune_path_id', $path->id)->where('tier', 3)->sortBy('slot_index')->values(),
            ];
        }

        // 3. ステータスルーンを段ごとに整理して取得
        $allStatRunes = \App\Models\StatRune::all();
        $statRunes = [
            $allStatRunes->where('tier', 0)->sortBy('slot_index')->values(),
            $allStatRunes->where('tier', 1)->sortBy('slot_index')->values(),
            $allStatRunes->where('tier', 2)->sortBy('slot_index')->values(),
        ];

        $validItems = $items->filter(function ($item) {
            return $item !== null && !empty($item->name) && !empty($item->image);
        });

        $itemTagMap = [
            'Damage' => '攻撃力',
            'AttackSpeed' => '攻撃速度',
            'CriticalStrike' => 'クリティカル',
            'LifeSteal' => 'ライフスティール',
            'ArmorPenetration' => '物理防御貫通',
            'SpellDamage' => '魔力',
            'CooldownReduction' => 'スキルヘイスト',
            'ManaRegen' => 'マナ自動回復',
            'MagicPenetration' => '魔法防御貫通',
            'Health' => '体力',
            'Armor' => '物理防御',
            'MagicResist' => '魔法防御',
            'HealthRegen' => '体力自動回復',
            'NonbootsMovement' => '移動速度',
            'Boots' => 'ブーツ',
        ];

        // 全てのデータをビューに渡す
        return view('posts.create', [
            'champions' => $champions,
            'lanes' => $lanes,
            'runePaths' => $runePaths,
            'runesByPath' => $runesByPath,
            'statRunes' => $statRunes,
            'items' => $validItems,
            'itemTagMap' => $itemTagMap,
        ]);
    }


    /**
     * 投稿保存処理
     */
    public function store(Request $request)
    {
        
        // バリデーションルールを新しいフォームに合わせて更新
        $validated = $request->validate([
            'title' => 'required|string|max:100|unique:posts',
            'content' => 'nullable|string',
            'champion_id' => 'required|exists:champions,id',
            'vs_champion_id' => 'nullable|exists:champions,id',
            'lane_id' => 'required|exists:lanes,id',
            'runes' => 'required|array', // 'json' から 'array' に変更
            'runes.*' => 'exists:runes,id',
            'stat_runes' => 'required|array|size:3',
            'stat_runes.*' => 'exists:stat_runes,id',
            'items' => 'nullable|array',
            'items.*' => 'exists:items,id',
        ]);

        // JSON形式のルーンIDをPHPの配列に変換
        $runeIds = $validated['runes'];
        $statRuneIds = $validated['stat_runes'];
        $itemIds = $validated['items'] ?? [];

        // 選択されたルーンの数が正しいかどうかの追加バリデーション
        if (count($runeIds) < 6) { // メイン4つ、サブ2つなど、ルールに合わせて調整
            return back()->withErrors(['runes' => 'ルーンの選択数が正しくありません。'])->withInput();
        }

        DB::beginTransaction();
        try {
            // 投稿本体を作成
            $post = Post::create([
                'user_id' => auth()->id(),
                'title' => $validated['title'],
                'content' => $validated['content'] ?? '',
                'champion_id' => $validated['champion_id'],
                'vs_champion_id' => $validated['vs_champion_id'],
                'lane_id' => $validated['lane_id'],
            ]);

            // 投稿と選択されたルーンを中間テーブル(post_rune)に保存
            $post->runes()->attach($runeIds);
            $post->statRunes()->attach($statRuneIds);

            // 投稿と選択されたアイテムを中間テーブル(post_item)に保存
            if (!empty($itemIds)) {
                $itemsToAttach = [];
                foreach ($itemIds as $index => $itemId) {
                    if ($itemId) {
                        $itemsToAttach[$itemId] = ['order' => $index];
                    }
                }
                $post->items()->attach($itemsToAttach);
            }

            DB::commit();
            return redirect()->route('posts.show', $post)->with('success', '投稿が作成されました！');
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('投稿保存エラー: ' . $e->getMessage());
            return back()->withErrors(['error' => '投稿の保存に失敗しました。']);
        }
    }

    /**
     * 投稿編集画面
     */
    public function edit(Post $post)
    {
        // 認可チェック
        $this->authorize('update', $post);

        // フォームの選択肢となる基本データを取得
        $champions = \App\Models\Champion::orderBy('name')->get();
        $lanes = \App\Models\Lane::all();
        $runePaths = \App\Models\RunePath::all();
        $allRunes = \App\Models\Rune::all();
        $items = \App\Models\Item::all(); // ※ createとの統一のため `$items` -> `$allItems` とします
        $allStatRunes = \App\Models\StatRune::all();

        // ルーンデータをパス・ティアごとに整理
        $runesByPath = [];
        foreach ($runePaths as $path) {
            $runesByPath[$path->id] = [
                $allRunes->where('rune_path_id', $path->id)->where('tier', 0)->sortBy('slot_index')->values(),
                $allRunes->where('rune_path_id', $path->id)->where('tier', 1)->sortBy('slot_index')->values(),
                $allRunes->where('rune_path_id', $path->id)->where('tier', 2)->sortBy('slot_index')->values(),
                $allRunes->where('rune_path_id', $path->id)->where('tier', 3)->sortBy('slot_index')->values(),
            ];
        }
        // ステータスルーンをティアごとに整理
        $statRunes = [
            $allStatRunes->where('tier', 0)->sortBy('slot_index')->values(),
            $allStatRunes->where('tier', 1)->sortBy('slot_index')->values(),
            $allStatRunes->where('tier', 2)->sortBy('slot_index')->values(),
        ];
        // アイテムのタグマップ
        $itemTagMap = [
            'Damage' => '攻撃力',
            'AttackSpeed' => '攻撃速度',
            'CriticalStrike' => 'クリティカル',
            'LifeSteal' => 'ライフスティール',
            'ArmorPenetration' => '物理防御貫通',
            'SpellDamage' => '魔力',
            'CooldownReduction' => 'スキルヘイスト',
            'ManaRegen' => 'マナ自動回復',
            'MagicPenetration' => '魔法防御貫通',
            'Health' => '体力',
            'Armor' => '物理防御',
            'MagicResist' => '魔法防御',
            'HealthRegen' => '体力自動回復',
            'NonbootsMovement' => '移動速度',
            'Boots' => 'ブーツ',
        ];

        // ★★★ここからが重要★★★
        // 編集する投稿の関連データを読み込む
        $post->load(['runes.runePath', 'statRunes', 'items']);

        // 選択済みルーンをパスごとに分類
        $runesByPathId = $post->runes->groupBy('rune_path_id');
        $mainPathRunes = $runesByPathId->sortByDesc(fn($group) => $group->count())->first() ?? collect();
        $subPathRunes = $runesByPathId->sortByDesc(fn($group) => $group->count())->slice(1)->first() ?? collect();

        // Alpine.jsで使いやすい形式に変換
        $selectedMainRunes = $mainPathRunes->keyBy('tier')->map(fn($rune) => $rune->id);
        $selectedSubRunes = $subPathRunes->keyBy('tier')->map(fn($rune) => $rune->id);
        $selectedStatRunes = $post->statRunes->keyBy('tier')->map(fn($rune) => $rune->id);

        // 選択済みアイテムをorder順に並べ、6個になるようにnullで埋める
        $selectedItems = $post->items->sortBy('pivot.order')->values();
        $paddedItems = $selectedItems->pad(6, null);
        // ★★★ここまでが重要★★★
        
        // すべての変数をビューに渡す
        return view('posts.edit', compact(
            'post', 'champions', 'lanes', 'runePaths', 'runesByPath', 'statRunes', 
            'items', 'itemTagMap', 
            'mainPathRunes', 'subPathRunes', 'selectedMainRunes', 
            'selectedSubRunes', 'selectedStatRunes', 'paddedItems'
        ));
    }

    /**
     * 投稿更新処理
     */
    public function update(Request $request, Post $post)
    {
        // 認可チェック
        $this->authorize('update', $post);

        // バリデーション (uniqueルールを更新)
        $validated = $request->validate([
            'title' => 'required|string|max:100|unique:posts,title,' . $post->id,
            // ... storeメソッドと同じルールが続く ...
        ]);

        $runeIds = $validated['runes'];
        $statRuneIds = $validated['stat_runes'];
        $itemIds = $validated['items'] ?? [];

        DB::beginTransaction();
        try {
            // Postモデルの更新
            $post->update([
                'title' => $validated['title'],
                'content' => $validated['content'] ?? '',
                'champion_id' => $validated['champion_id'],
                'vs_champion_id' => $validated['vs_champion_id'] ?? null,
                'lane_id' => $validated['lane_id'],
            ]);

            // 関連データを sync() で更新
            $post->runes()->sync($runeIds);
            $post->statRunes()->sync($statRuneIds);
            
            $itemsToSync = [];
            foreach ($itemIds as $index => $itemId) {
                if ($itemId) {
                    $itemsToSync[$itemId] = ['order' => $index];
                }
            }
            $post->items()->sync($itemsToSync);

            DB::commit();
            return redirect()->route('posts.show', $post)->with('success', '投稿が更新されました！');
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('投稿更新エラー: ' . $e->getMessage());
            return back()->withErrors(['error' => '投稿の更新に失敗しました。']);
        }
    }

    /**
     * 投稿削除処理
     */
    public function destroy(Post $post)
    {
        // 認可チェック
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()->route('posts.index')->with('success', '投稿を削除しました。');
    }
}
