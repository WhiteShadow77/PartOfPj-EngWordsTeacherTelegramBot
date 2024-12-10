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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_enabled_english_words_sending')
                ->default(true)
                ->after('remember_token');
                        $table->boolean('is_enabled_quiz_sending')
                ->default(true)
                ->after('is_enabled_english_words_sending');
            $table->integer('english_words_week_sending_conf') //7 bits codes days of week
            ->default(1)
                ->after('is_enabled_english_words_sending');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_enabled_english_words_sending','english_words_week_sending_conf']);
        });
    }
};
