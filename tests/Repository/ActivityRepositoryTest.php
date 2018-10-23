<?php

namespace Tests\Feature\Repository;

use App\Repositories\ActivityRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ActivityRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /** @var ActivityRepository */
    private $repository;

    protected function setUp()
    {
        parent::setUp();

        $this->repository = new ActivityRepository();
    }

    /** @test */
    public function is_weekly_retention_fetched()
    {
        $this->seed(\ActivityLogTestingSeeder::class);

        $result = '[{"name":"Week 1","data":[100,100,33,33,0,0,0,0]},{"name":"Week 2","data":[100,100,100,67,67,67,67,67]}]';

        $this->assertEquals($result, $this->repository->fetchWeeklyRetention()->toJson());
    }

    /** @test */
    public function is_weekly_data_set()
    {
        $weeklyData = [
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
                'onboarding_percentage' => 65,
                'week'                  => 1,
                'count'                 => 10,
            ],
        ];

        $this->repository->setWeeklyData(collect($weeklyData));

        $this->assertEquals($weeklyData, $this->repository->weeklyData->toArray());
    }

    /** @test */
    public function is_weekly_data_filtered_for_unused_percentages()
    {
        $weeklyData = [
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
                'onboarding_percentage' => 65,
                'week'                  => 1,
                'count'                 => 10,
            ],
        ];

        $this->repository->setWeeklyData(collect($weeklyData));

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

        $this->assertEquals(collect($expected),
            $this->repository->dropUnusedOnBoardingPercentageData()->getWeeklyData());
    }

    /** @test */
    public function is_missing_percentage_added_to_weekly_data()
    {
        $weeklyData = [
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

        $this->repository->setWeeklyData(collect($weeklyData));

        $expected = [
            [
                'onboarding_percentage' => 0,
                'week'                  => 1,
                'count'                 => 0,
            ],
            [
                'onboarding_percentage' => 20,
                'week'                  => 1,
                'count'                 => 2,
            ],
            [
                'onboarding_percentage' => 40,
                'week'                  => 1,
                'count'                 => 0,
            ],
            [
                'onboarding_percentage' => 50,
                'week'                  => 1,
                'count'                 => 1,
            ],
            [
                'onboarding_percentage' => 70,
                'week'                  => 1,
                'count'                 => 0,
            ],
            [
                'onboarding_percentage' => 90,
                'week'                  => 1,
                'count'                 => 0,
            ],
            [
                'onboarding_percentage' => 99,
                'week'                  => 1,
                'count'                 => 0,
            ],
            [
                'onboarding_percentage' => 100,
                'week'                  => 1,
                'count'                 => 0,
            ],
        ];

        $this->assertEquals($expected,
            $this->repository->addMissingOnBoardingPercentageData()->getWeeklyData()->toArray());
    }

    /** @test */
    public function is_total_count_calculated_per_step()
    {
        $weeklyData = [
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

        $this->repository->setWeeklyData(collect($weeklyData));

        $expected = [
            [
                'onboarding_percentage' => 20,
                'week'                  => 1,
                'count'                 => 2,
                'totalCount'            => 3,
            ],
            [
                'onboarding_percentage' => 50,
                'week'                  => 1,
                'count'                 => 1,
                'totalCount'            => 1,
            ],
        ];

        $this->assertEquals(collect($expected), $this->repository->calculateTotalCountPerWeeklyStep()->getWeeklyData());
    }

    /** @test */
    public function is_percentage_calculated_correct()
    {
        $percentage = $this->repository->calculatePercentage(20, 100);

        $this->assertEquals(20, $percentage);
    }
}
