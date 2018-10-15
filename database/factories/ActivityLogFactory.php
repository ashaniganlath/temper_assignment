<?php

use App\Activity;
use Faker\Generator as Faker;

$factory->define(Activity::class, function (Faker $faker) {
    return [
        'user_id'               => $faker->unique()->numberBetween('3121', '3530'),
        'onboarding_percentage' => $faker->randomElement(['0', '20', '40', '50', '70', '90', '99', '100']),
        'created_at'            => $faker->dateTimeBetween('7/19/16', '8/10/16'),
    ];
});
