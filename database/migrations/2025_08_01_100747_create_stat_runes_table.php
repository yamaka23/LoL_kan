<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stat_runes', function (Blueprint $table) {
            $table->id(); // データベース管理用の、シンプルな自動増分IDを主キーにします
            
            $table->integer('api_id'); // Riot APIが使うIDは、こちらに保存します
            $table->string('name');
            $table->string('icon_path');
            $table->integer('tier'); // 0:攻撃, 1:フレックス, 2:防御
            $table->integer('slot_index');
            $table->timestamps();

            // 「APIのID」と「段」の組み合わせは、重複しないようにします
            $table->unique(['api_id', 'tier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stat_runes');
    }
};
