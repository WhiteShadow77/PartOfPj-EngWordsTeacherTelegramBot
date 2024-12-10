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
    public function up()
    {
        Schema::table('english_word_russian_word', function ($table) {
            $table->dropForeign(['english_word_id']);
            $table->foreign('english_word_id')->references('id')
                ->on('english_words')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('english_word_russian_word', function ($table) {
            $table->dropForeign(['english_word_id']);
            $table->foreign('english_word_id')->references('id')
                ->on('english_words')->onDelete('SET NULL');
        });
    }
};
