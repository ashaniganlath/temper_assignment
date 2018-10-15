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
    public function is_percentage_calculated_correct()
    {
        $percentage = $this->repository->calculatePercentage(20, 100);

        $this->assertEquals(20, $percentage);
    }

    /** @test */
    public function is_weekly_retention_fetched()
    {
        $this->seed(\ActivityLogTestingSeeder::class);

        $weeklyRetention = $this->repository->fetchWeeklyRetention();

        $expected = '[{"name":"Week 1","data":[100,67]},{"name":"Week 2","data":[100]}]';

        $this->assertEquals($expected, $weeklyRetention->toJson());
    }

    /** @test */
    public function is_total_count_per_step_calculated()
    {
        $weeklyData = [
            [
                'onboarding_percentage' => 20,
                'week'                  => 1,
                'count'                 => 2,
            ],
            [
                'onboarding_percentage' => 100,
                'week'                  => 1,
                'count'                 => 3,
            ],
        ];

        $expected = [
            [
                'onboarding_percentage' => 20,
                'week'                  => 1,
                'count'                 => 2,
                'totalCount'            => 5,
            ],
            [
                'onboarding_percentage' => 100,
                'week'                  => 1,
                'count'                 => 3,
                'totalCount'            => 3,
            ],
        ];

        $result = $this->repository->calculateTotalCountPerStep(collect($weeklyData));

        $this->assertEquals($expected, $result->toArray());
    }
}
