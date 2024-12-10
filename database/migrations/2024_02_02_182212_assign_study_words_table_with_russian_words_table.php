<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('study_words', function (Blueprint $table) {
            $table->foreignId('english_words_id')->nullable();
            $table->foreign('english_words_id')
                ->references('id')
                ->on('english_words');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('study_words', function (Blueprint $table) {
            $table->dropColumn('english_words_id');
        });
    }
};
