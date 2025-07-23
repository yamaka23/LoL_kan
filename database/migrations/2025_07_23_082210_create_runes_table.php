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
        Schema::create('runes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // ルーン名は一意である必要があります
            $table->string('icon')->nullable(); // 画像URLまたはファイル名
            $table->foreignId('main_rune_id')->constrained('main_runes')->onDelete('cascade');
            $table->text('longDesc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('runes');
    }
};
