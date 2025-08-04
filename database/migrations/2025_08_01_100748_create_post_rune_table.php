<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 投稿と選択されたルーンを紐付けるための中間テーブル
        Schema::create('post_rune', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            
            // runesテーブルのidはintegerなので型を合わせる
            $table->integer('rune_id');
            $table->foreign('rune_id')->references('id')->on('runes')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_rune');
    }
};
