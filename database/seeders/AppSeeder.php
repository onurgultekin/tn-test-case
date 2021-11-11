<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\App;
use Faker\Factory as Faker;

class AppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        for($i=0; $i < 250; $i++) {
            App::create([
                'name' => $faker->name,
            ]);
        }
    }
}
