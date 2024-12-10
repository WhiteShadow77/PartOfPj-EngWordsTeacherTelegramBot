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
        Schema::create('known_words_ids', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('english_word_id');
            $table->foreignId('user_id')->unsigned();
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
        Schema::dropIfExists('known_words_ids');
    }
};
