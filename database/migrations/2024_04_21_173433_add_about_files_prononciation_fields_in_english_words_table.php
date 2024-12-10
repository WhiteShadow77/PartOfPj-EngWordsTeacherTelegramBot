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
            $table->string('uk_pron_file')
                ->nullable()
                ->default(null)
                ->after('word');
            $table->string('us_pron_file')
                ->nullable()
                ->default(null)
                ->after('uk_pron_file');
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
            $table->dropColumn(['uk_pron_file', 'us_pron_file']);
        });
    }
};
