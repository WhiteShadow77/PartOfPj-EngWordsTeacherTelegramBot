<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('statistics', function (Blueprint $table) {
            $table->foreignId('english_word_id')
                ->nullable()
                ->default(null)
                ->after('user_id');
            $table
                ->foreign('english_word_id')
                ->references('id')
                ->on('english_words')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('statistics', function (Blueprint $table) {
            $table->dropForeign('statistics_english_word_id_foreign');
            $table->dropColumn('english_word_id');
        });
    }
};
