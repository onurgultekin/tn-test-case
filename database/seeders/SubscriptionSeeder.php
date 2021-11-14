<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subscription;
use Faker\Factory as Faker;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        for($i=0; $i < 15000; $i++) {
            Subscription::create([
                'status' => $faker->randomElement($array = array ('Started','Renewed', 'Cancelled')),
                'device_id' => $faker->numberBetween($min = 1, $max = 10),
                'app_id' => $faker->numberBetween($min = 1, $max = 250),
                'receipt' => $faker->numberBetween($min = 10000000, $max = 99000000),
                'expired_at' => $faker->dateTimeBetween($startDate = '-10 years', $endDate = '+10 years', $timezone = 'America/Tijuana'),
            ]);
        }
    }
}
