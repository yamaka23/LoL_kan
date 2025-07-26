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

Route::get('/', function () {
    return view('welcome');
});

// API同期ルート（自分だけアクセス可）
Route::middleware(['auth', 'can:sync-data'])->group(function () {
    Route::get('/sync-champions', [ChampionController::class, 'syncChampions']);
    Route::get('/sync-runes', [RuneController::class, 'syncRunes']);
    Route::get('/sync-items', [ItemController::class, 'syncItems']);
});

// チャンピオン表示（誰でもOK）
Route::get('/champions', [ChampionController::class, 'listChampions']);
Route::get('/posts/champion/{champion_id}', [PostController::class, 'championIndex'])->name('posts.champion');


// ダッシュボード（ログインユーザー）
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 投稿一覧・詳細（誰でもOK）
Route::resource('posts', PostController::class)->only(['index', 'show']);

// 投稿・いいね（ログインユーザーのみ）
Route::middleware('auth')->group(function () {
    Route::resource('posts', PostController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::post('/posts/{post}/like', [PostController::class, 'like'])->name('posts.like');

    // プロフィール編集など
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Breezeの認証ルート（ログイン・登録）
require __DIR__.'/auth.php';
