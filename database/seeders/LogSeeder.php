<?php

namespace Database\Seeders;

use App\Models\Log;
use App\Models\WebAccount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Log::factory(1)->create();
    }
}
