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
        Schema::create('champions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');// チャンピオン名は一意である必要があります
            $table->string('image_url')->nullable();
            $table->string('language')->default('ja'); // デフォルトは日本語
            $table->string('version')->nullable(); // デフォルトは最新バージョン
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('champions');
    }
};
