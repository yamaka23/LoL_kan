<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChampionController;
use App\Http\Controllers\RuneController;
use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// トップページ
Route::get('/', function () {
    return view('welcome');
});

//==============================
// ▼ データ同期（管理者専用）
//==============================
Route::middleware(['auth', 'can:sync-data'])->group(function () {
    Route::get('/sync-champions', [ChampionController::class, 'syncChampions']);
    Route::get('/sync-runes', [RuneController::class, 'syncRunes']);
    Route::get('/sync-items', [ItemController::class, 'syncItems']);
});

//==============================
// ▼ チャンピオン表示（誰でもアクセス可）
//==============================
Route::get('/champions', [ChampionController::class, 'listChampions']);

//==============================
// ▼ 投稿表示（誰でもアクセス可）
//==============================

// チャンピオン別の投稿一覧（※注意：resourceより前に定義！）
Route::get('/posts/champion/{champion_id}', [PostController::class, 'championIndex'])->name('posts.champion');

Route::get('/champions/{champion}/posts', [PostController::class, 'championIndex'])->name('champions.posts');

// 全投稿一覧・詳細表示
Route::resource('posts', PostController::class)->only(['index', 'show']);

//==============================
// ▼ 投稿・プロフィール（ログインユーザーのみ）
//==============================
Route::middleware('auth')->group(function () {
    // 投稿作成・編集・削除・いいね
    Route::resource('posts', PostController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::post('/posts/{post}/like', [PostController::class, 'like'])->name('posts.like');

    // プロフィール
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//==============================
// ▼ ダッシュボード（ログイン済み + メール認証）
//==============================
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//==============================
// ▼ Breeze認証ルート（ログイン・登録など）
//==============================
require __DIR__.'/auth.php';
