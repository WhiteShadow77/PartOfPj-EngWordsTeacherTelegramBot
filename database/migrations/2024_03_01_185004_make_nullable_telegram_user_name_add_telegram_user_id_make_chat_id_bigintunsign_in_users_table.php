<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->string('telegram_user_name')->nullable()->default(null)->change();
            $table->unsignedBigInteger('chat_id')->change();
            $table->string('telegram_user_id')->nullable()->default(null)->after('telegram_user_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::table('users', function (Blueprint $table) {
//            $table->string('telegram_user_name_new')
//                ->after('telegram_user_name');
//        });
//        DB::table('users')->update([
//            'telegram_user_name_new' => is_null(DB::raw('telegram_user_name')) ? '' : DB::raw('telegram_user_name')
//        ]);
        Schema::table('users', function (Blueprint $table) {
//            $table->dropColumn('telegram_user_name');
//            $table->renameColumn('telegram_user_name_new', 'telegram_user_name');
            $table->dropColumn(['telegram_user_id']);
        });
    }
};
