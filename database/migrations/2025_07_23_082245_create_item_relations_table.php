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
        Schema::create('item_relations', function (Blueprint $table) {
            $table->id();
            // 完成アイテムの外部キー（itemsテーブルに存在）
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');

            // 素材の外部キー（item_materialsに存在）
            $table->foreignId('material_item_id')->constrained('item_materials')->onDelete('cascade');


            //  同じ素材が複数必要な場合の数量を追加
            $table->unsignedTinyInteger('count')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_relations');
    }
};
