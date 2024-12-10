<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $database           = config('database.default');
        $databaseUserName   = config('database.connections.' . $database . '.username');

        $definer = 'DEFINER=`' . $databaseUserName . '`@`localhost`';

        DB::unprepared('
            CREATE ' . $definer . ' TRIGGER `english_words_trigger` BEFORE DELETE ON `english_words` FOR EACH ROW
                DELETE FROM russian_words where russian_words.id in (
                    SELECT english_word_russian_word.russian_word_id from english_word_russian_word
                        WHERE english_word_russian_word.english_word_id = OLD.id
                    )
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER `english_words_trigger`');
    }
};
