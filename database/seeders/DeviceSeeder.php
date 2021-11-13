<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Device;
use Faker\Factory as Faker;
use Hash;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        for($i=0; $i < 10; $i++) {
            Device::create([
                'app_id' => $faker->numberBetween($min = 1, $max = 250),
                'uuid' => $faker->uuid,
                'language' => $faker->languageCode,
                'os' => $faker->randomElement($array = array ('ios','android')),
                'client_token' => Hash::make($faker->uuid),
            ]);
        }
    }
}
