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
        Schema::create('post_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade'); // 投稿IDは外部キー制約
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade'); // アイテムIDは外部キー制約
            $table->integer('order')->nullable(); // 装備順
            $table->unique(['post_id', 'item_id']); // 複合ユニーク
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_item');
    }
};
