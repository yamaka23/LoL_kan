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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->string('plaintext')->nullable();
            $table->json('tags')->nullable();
            $table->json('stats')->nullable();
            $table->json('gold')->nullable();
            $table->boolean('in_store')->default(true);
            $table->boolean('purchasable')->default(true);
            $table->string('language')->default('ja_JP'); // デフォルトは日本語
            $table->integer('depth')->nullable(); // アイテムの深さ（ツリー構造用）
            $table->string('required_champion')->nullable(); // チャンピオン固有のアイテム
            $table->boolean('is_summoners_rift_available')->default(false); // サモナーズリフトで利用可能かどうか
            $table->string('version')->nullable(); // デフォルトは最新バージョン
            $table->text('colloq')->nullable(); //  略称をカンマ区切りで保持
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
