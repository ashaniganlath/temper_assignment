<?php

use Illuminate\Database\Seeder;

class ActivityLogTestingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Activity::insert([
            [
                'user_id'               => 01,
                'onboarding_percentage' => 20,
                'created_at'            => '2018-01-02',
            ],
            [
                'user_id'               => 02,
                'onboarding_percentage' => 50,
                'created_at'            => '2018-01-03',
            ],
            [
                'user_id'               => 03,
                'onboarding_percentage' => 20,
                'created_at'            => '2018-01-07',
            ],
            [
                'user_id'               => 04,
                'onboarding_percentage' => 100,
                'created_at'            => '2018-01-08',
            ],
            [
                'user_id'               => 05,
                'onboarding_percentage' => 100,
                'created_at'            => '2018-01-09',
            ],
            [
                'user_id'               => 06,
                'onboarding_percentage' => 40,
                'created_at'            => '2018-01-14',
            ],
        ]);
    }
}
