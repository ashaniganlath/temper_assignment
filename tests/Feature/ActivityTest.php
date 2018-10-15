<?php

namespace Tests\Feature;

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

    /**
     * @test
     */
    public function is_data_grouped_by_week()
    {
        $response = $this->json('GET', '/api/activities');

        $response->assertStatus(200);

        $this->assertCount(2, $response->json());
        $this->assertArraySubset([
            [
                'name' => 'Week 1',
                'data' => [100, 67],
            ],
            [
                'name' => 'Week 2',
                'data' => [100],
            ],
        ], $response->json());
    }
}
