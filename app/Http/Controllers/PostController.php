<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Champion;
use App\Models\Lane;
use App\Models\Item;
use App\Models\Rune;
use Illuminate\Support\Facades\DB;
use App\Models\PostItem;
use App\Models\PostRunePath;



class PostController extends Controller
{
    // 投稿一覧（全ユーザーに公開）
    public function index()
    {
        $posts = Post::with('user')->latest()->paginate(10);
        return view('posts.index', compact('posts'));
    }

    // チャンピオンごとの投稿一覧
    public function championIndex(Champion $champion)
    {
 

        $posts = Post::where('champion_id', $champion->id)
            ->with('lane', 'user')
            ->latest()
            ->paginate(10);

        return view('posts.index', compact('posts', 'champion'));
    }

    // 投稿詳細
    public function show($id)
    {
        $post = Post::with('user')->findOrFail($id);
        return view('posts.show', compact('post'));
    }

    // 投稿作成画面（ログイン必須）
    public function create()
    {
        $champions= Champion::orderBy('name')->get();
        $lanes = Lane::all();
        $items = Item::orderBy('name')->get();
        $runes = Rune::orderBy('name')->get();
        return view('posts.create', compact('champions', 'lanes', 'items', 'runes'));
    }

    // 投稿保存処理
    public function store(Request $request)
    {

        $request->validate([
            'title' => 'required|string|max:100|unique:posts',
            'content' => 'nullable|string',
            'champion_id' => 'required|exists:champions,id',
            'vs_champion_id' => 'nullable|exists:champions,id',
            'lane_id' => 'required|exists:lanes,id',
            'items' => 'nullable|array|max:6',
            'items.*' => 'nullable|exists:items,id',
            'main_rune_path' => 'required|exists:runes,id',
            'main_runes' => 'array|size:4',
            'main_runes.*' => 'nullable|exists:runes,id',
            'sub_rune_path' => 'required|exists:runes,id',
            'sub_runes' => 'array|size:2',
            'sub_runes.*' => 'nullable|exists:runes,id',
            'stat_runes' => 'array|size:3',
            'stat_runes.*' => 'nullable|exists:runes,id',
        ]);

        DB::beginTransaction();
        try {
            // 投稿本体
            $post = Post::create([
                'user_id' => auth()->id(),
                'champion_id' => $request->champion_id,
                'vs_champion_id' => $request->vs_champion_id,
                'lane_id' => $request->lane_id,
                'title' => $request->title,
                'content' => $request->content,
            ]);

            // アイテム（順序つき）
            if ($request->has('items')) {
                foreach ($request->items as $index => $itemId) {
                    if ($itemId) {
                        $post->items()->attach($itemId, ['order' => $index + 1]);
                    }
                }
            }

            // ルーン：ルート保存
            $runePath = PostRunePath::create([
                'post_id' => $post->id,
                'main_rune_id' => $request->main_rune_path,
                'sub_rune_id' => $request->sub_rune_path,
            ]);

            // ルーン：個別保存
            $allRunes = collect($request->main_runes)->map(fn($id) => ['id' => $id, 'type' => 'main'])
                ->concat(collect($request->sub_runes)->map(fn($id) => ['id' => $id, 'type' => 'sub']))
                ->concat(collect($request->stat_runes)->map(fn($id) => ['id' => $id, 'type' => 'stat']));

            foreach ($allRunes as $rune) {
                if (!empty($rune['id'])) {
                    $post->runes()->attach($rune['id'], ['slot_type' => $rune['type']]);
                }
            }

            DB::commit();
            return redirect()->route('posts.show', $post)->with('success', '投稿が作成されました！');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => '保存に失敗しました: ' . $e->getMessage()]);
        }
    }

    // 投稿編集画面（投稿者のみ）
    public function edit($id)
    {
        $post = Post::findOrFail($id);

        // 投稿者のみ許可
        if (Auth::id() !== $post->user_id) {
            abort(403);
        }

        $champions = Champion::orderBy('name')->get();
        $lanes = Lane::all();
        $items = Item::orderBy('name')->get();
        $runes = Rune::orderBy('name')->get();

        return view('posts.edit', compact('post', 'champions', 'lanes', 'items', 'runes'));
    }

    // 投稿更新処理
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        if (Auth::id() !== $post->user_id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|max:100',
            'content' => 'nullable|string',
        ]);

        $post->update($validated);

        return redirect()->route('posts.show', $post)->with('success', '投稿を更新しました');
    }

    // 投稿削除処理
    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        if (Auth::id() !== $post->user_id) {
            abort(403);
        }

        $post->delete();

        return redirect()->route('posts.index')->with('success', '投稿を削除しました');
    }

    // いいね（ログイン必須、別途処理可）
    public function like($id)
    {
        // あとで実装（中間テーブル使う想定）
        return back();
    }
}
