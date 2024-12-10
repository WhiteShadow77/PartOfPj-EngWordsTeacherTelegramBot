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
        Schema::table('web_accounts', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->change()
                ->nullable()
                ->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('web_accounts', function (Blueprint $table) {
            //$table->dropColumn('telegram_user_id');
            $table->foreignId('user_id')->change();
        });
    }
};
