<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChampionController;
use App\Http\Controllers\RuneController;
use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- パブリックルート（誰でもアクセス可能） ---

Route::get('/', function () {
    return view('welcome');
});

Route::get('/champions', [ChampionController::class, 'listChampions'])->name('champions.index');
Route::get('/champions/{champion}/posts', [PostController::class, 'championIndex'])->name('champions.posts');

// 投稿の一覧(index)と詳細(show)は誰でも見れる
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');


// --- 認証済みユーザー専用ルート ---

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ChampionController::class, 'dashboard'])->middleware('verified')->name('dashboard');

    // プロフィール編集関連
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // 投稿の作成、保存、編集、更新、削除はログインが必要
    Route::get('/new-post-form', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    
    // いいね機能
    Route::post('/posts/{post}/like', [PostController::class, 'like'])->name('posts.like');
});


// --- 管理者専用ルート ---

Route::middleware(['auth', 'can:sync-data'])->group(function () {
    Route::get('/sync-champions', [ChampionController::class, 'syncChampions'])->name('sync.champions');
    Route::get('/sync-runes', [RuneController::class, 'syncRunes'])->name('sync.runes');
    Route::get('/sync-items', [ItemController::class, 'syncItems'])->name('sync.items');
});


// --- 認証関連ルート ---

require __DIR__.'/auth.php';
