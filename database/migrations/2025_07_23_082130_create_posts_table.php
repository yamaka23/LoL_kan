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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ユーザーIDは外部キー制約
            
            $table->string('champion_id');
            $table->string('vs_champion_id')->nullable();

            $table->foreign('champion_id')->references('id')->on('champions')->onDelete('cascade');
            $table->foreign('vs_champion_id')->references('id')->on('champions')->onDelete('cascade');
            
            $table->foreignId('lane_id')->constrained()->onDelete('cascade'); // レーンIDは外部キー制約
            $table->string('title',100)->unique(); // 投稿のタイトル
            $table->text('content')->nullable(); // 投稿の内容
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
