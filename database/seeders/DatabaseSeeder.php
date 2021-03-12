<?php

namespace Database\Seeders;

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
        // \App\Models\User::factory(10)->create();
        \App\Models\User::factory(User::class)->create([
            'email' =>  "samoilenko@gmail.com",
            'name'  =>  "Dmitry"
        ]);
        \App\Models\User::factory(User::class)->create([
            'email' =>  "sanya@gmail.com",
            'name'  =>  "Alex"
        ]);
        \App\Models\User::factory(User::class)->create([
            'email' =>  "jurager@gmail.com",
            'name'  =>  "Yura"
        ]);
        \App\Models\User::factory(User::class)->create([
            'email' =>  "indus@gmail.com",
            'name'  =>  "Sartak"
        ]);
    }
}
