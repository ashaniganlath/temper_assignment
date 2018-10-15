<?php

use Illuminate\Database\Seeder;

class ActivityLogTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Activity::class, 350)->create();
    }
}
