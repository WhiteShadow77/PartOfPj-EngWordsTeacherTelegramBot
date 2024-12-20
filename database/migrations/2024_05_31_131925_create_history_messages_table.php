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
        Schema::create('history_messages', function (Blueprint $table) {
            $table->id();
            $table->text('text')->nullable()->default(null);
            $table->string('arguments')->nullable()->default(null);
            $table->unsignedBigInteger('word_id')->nullable()->default(null);
            $table->foreignId('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
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
        Schema::dropIfExists('history_messages');
    }
};
