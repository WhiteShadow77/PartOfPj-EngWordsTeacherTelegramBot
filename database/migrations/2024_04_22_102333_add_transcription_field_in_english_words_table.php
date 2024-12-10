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
        Schema::table('english_words', function (Blueprint $table) {
            $table->string('transcription')
                ->nullable()
                ->default(null)
                ->after('word');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('english_words', function (Blueprint $table) {
            $table->dropColumn('transcription');
        });
    }
};
