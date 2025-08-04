<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. ルーンのパス（覇道、栄華など）を保存するテーブルを作成
        Schema::create('rune_paths', function (Blueprint $table) {
            $table->integer('id')->primary(); // APIのIDをそのまま使う
            $table->string('key')->unique();
            $table->string('name');
            $table->string('icon_path');
            $table->timestamps();
        });

        // 2. 個々のルーンを保存するテーブルを作成
        Schema::create('runes', function (Blueprint $table) {
            $table->integer('id')->primary(); // APIのIDをそのまま使う
            $table->string('name');
            $table->string('icon_path');
            $table->text('longDesc');

            // 親であるパスへの関連付け
            $table->integer('rune_path_id');
            $table->foreign('rune_path_id')->references('id')->on('rune_paths')->onDelete('cascade');

            // パス内での位置情報
            $table->integer('tier');       // 0:キーストーン, 1:1段目...
            $table->integer('slot_index'); // その段の何番目か

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('runes');
        Schema::dropIfExists('rune_paths');
    }
};
