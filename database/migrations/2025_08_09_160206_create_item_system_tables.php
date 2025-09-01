<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. アイテム自体の情報を保存するテーブル
        Schema::create('items', function (Blueprint $table) {
            $table->integer('id')->primary(); // APIのIDを主キーにする
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('plaintext')->nullable();
            $table->integer('gold');
            $table->string('image');
            $table->string('version');
            $table->timestamps();
        });

        // 2. 投稿とアイテムの関連（順番も）を保存する中間テーブル
        Schema::create('post_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            
            // itemsテーブルのidはintegerなので型を合わせる
            $table->integer('item_id'); 
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');

            $table->integer('order'); // 1番目から6番目までの順番を保存
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_item');
        Schema::dropIfExists('items');
    }
};
