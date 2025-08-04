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
        // 投稿に紐づくルーン情報も一緒に読み込む
        $post->load('runes.runePath');
        return view('posts.show', compact('post'));
    }

    /**
     * 投稿作成画面
     */
    public function create()
    {
        // フォームの基本情報を取得
        $champions = \App\Models\Champion::orderBy('name')->get();
        $lanes = \App\Models\Lane::all();

        // --- ▼ ルーンUI用のデータ準備 ▼ ---

        // 1. パスの一覧を取得 (覇道、栄華など)
        $runePaths = \App\Models\RunePath::all();

        // 2. メインルーンをパスごと、段ごとに整理
        $allRunes = \App\Models\Rune::all();
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

        // --- ▲ ルーンUI用のデータ準備 ▲ ---

        // 全てのデータをビューに渡す
        return view('posts.create', [
            'champions' => $champions,
            'lanes' => $lanes,
            'runePaths' => $runePaths,
            'runesByPath' => $runesByPath,
            'statRunes' => $statRunes, // ステータスルーンのデータを追加
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
            'lane_id' => 'required|exists:lanes,id',
            'runes' => 'required|json', // UIからJSON形式で送られてくる
        ]);

        // JSON形式のルーンIDをPHPの配列に変換
        $runeIds = json_decode($validated['runes']);

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
                'lane_id' => $validated['lane_id'],
            ]);

            // 投稿と選択されたルーンを中間テーブル(post_rune)に保存
            if (!empty($runeIds)) {
                $post->runes()->attach($runeIds);
            }

            DB::commit();
            return redirect()->route('posts.show', $post)->with('success', '投稿が作成されました！');
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('投稿保存エラー: ' . $e->getMessage());
            return back()->withErrors(['error' => '投稿の保存に失敗しました。']);
        }
    }

    // edit, update, destroy メソッドは、今後ルーン編集機能を追加する際に同様に修正が必要です。
    // ...
}
