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
        Schema::create('known_words', function (Blueprint $table) {
            $table->id();
            $table->foreignId('english_word_id'); //->unique();
            $table->foreign('english_word_id')
                ->references('id')
                ->on('english_words')
                ->onDelete('CASCADE');
            $table->foreignId('user_id');
            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('CASCADE');
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
        Schema::dropIfExists('known_words');
    }
};
