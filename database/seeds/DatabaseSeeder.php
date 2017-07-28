<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ApplicationTableSeeder::class);
    }
}

class ApplicationTableSeeder extends Seeder
{
    public function run()
    {
        \App\Application::create(['user_id' => 1, 'key' => '6MJdVYFGxBN0g0Hc', 'secret' => 'kBFmRqtTFhNyQK4BkHSEEZlwyw5rRL53E61OGpOIluhd0c6kLenprQobZWFcCbE5']);
    }
}