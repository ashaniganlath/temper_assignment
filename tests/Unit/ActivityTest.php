<?php

namespace Tests\Unit;

use App\Activity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->seed(\ActivityLogTestingSeeder::class);
    }

    /** @test */
    public function is_data_grouped_by_week_and_onboarding_percentage()
    {
        $expected = [
            [
                'onboarding_percentage' => 20,
                'week'                  => 1,
                'count'                 => 2,
            ],
            [
                'onboarding_percentage' => 50,
                'week'                  => 1,
                'count'                 => 1,
            ],
            [
                'onboarding_percentage' => 40,
                'week'                  => 2,
                'count'                 => 1,
            ],
            [
                'onboarding_percentage' => 100,
                'week'                  => 2,
                'count'                 => 2,
            ],
        ];

        $this->assertEquals($expected, Activity::weekly()->get()->toArray());
    }

    /** @test */
    public function is_percentage_retrieved_by_order_per_week()
    {
        $expected = [
            [
                'onboarding_percentage' => 20,
                'week'                  => 1,
                'count'                 => 2,
            ],
            [
                'onboarding_percentage' => 50,
                'week'                  => 1,
                'count'                 => 1,
            ],
        ];

        $this->assertEquals($expected, Activity::weekly()->where('week', 1)->get()->toArray());
    }
}
