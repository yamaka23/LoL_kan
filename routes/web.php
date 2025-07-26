<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChampionController;
use App\Http\Controllers\RuneController;
use App\Http\Controllers\ItemController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


// API同期ルート
Route::get('/sync-champions', [ChampionController::class, 'syncChampions']);
Route::get('/sync-runes', [RuneController::class, 'syncRunes']);
Route::get('/sync-items', [ItemController::class, 'syncItems']);


Route::get('/champions', [ChampionController::class, 'listChampions']);

// ダッシュボード（ログイン済ユーザーのみ）
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 投稿一覧・詳細（誰でも見れる）
Route::resource('posts', PostController::class)->only(['index', 'show']);

// ログインユーザーのみ：投稿作成・編集・削除・いいね
Route::middleware('auth')->group(function () {
    Route::resource('posts', PostController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::post('/posts/{post}/like', [PostController::class, 'like'])->name('posts.like');

    // プロフィール編集など（Breeze初期ルート）
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Breezeの認証ルート（ログイン・登録など）
require __DIR__.'/auth.php';

