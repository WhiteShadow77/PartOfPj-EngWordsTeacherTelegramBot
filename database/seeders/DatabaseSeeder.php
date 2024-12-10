<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Log;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory(1)->create();
        Log::factory(1)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'TwitchTestCommand User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
