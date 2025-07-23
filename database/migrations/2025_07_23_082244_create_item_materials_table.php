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
        Schema::create('item_materials', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();//素材・中間アイテムが入る
            $table->string('image_url');
            $table->json('stats')->nullable();
            $table->integer('gold')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_materials');
    }
};
