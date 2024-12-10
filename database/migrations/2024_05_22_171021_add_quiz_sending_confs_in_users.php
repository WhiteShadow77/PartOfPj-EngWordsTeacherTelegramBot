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
            $table->integer('quiz_week_sending_conf')
                ->default(1)
                ->after('english_words_week_sending_conf');
            $table->json('quiz_week_and_times_sending_conf')
                ->nullable()
                ->default(null)
                ->after('quiz_week_sending_conf');
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
            $table->dropColumn([
                'quiz_week_sending_conf',
                'quiz_week_and_times_sending_conf'
            ]);
        });
    }
};
