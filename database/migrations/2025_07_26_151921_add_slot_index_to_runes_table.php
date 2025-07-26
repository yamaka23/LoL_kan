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
        Schema::table('runes', function (Blueprint $table) {
            $table->unsignedTinyInteger('slot_index')->nullable()->after('longDesc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('runes', function (Blueprint $table) {
            $table->dropColumn('slot_index');
        });
    }
};
