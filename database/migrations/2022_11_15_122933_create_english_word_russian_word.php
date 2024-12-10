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
    public function up()
    {
        Schema::create('english_word_russian_word', function (Blueprint $table) {
            $table->foreignId('english_word_id')->unsigned()->nullable();
            $table->foreign('english_word_id')->references('id')
                ->on('english_words')->onDelete('SET NULL');
            $table->foreignId('russian_word_id')->unsigned()->nullable();
            $table->foreign('russian_word_id')->references('id')
                ->on('russian_words')->onDelete('SET NULL');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('english_word_russian_word');
    }
};
