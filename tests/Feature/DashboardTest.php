<?php

namespace Tests\Feature;

use Tests\TestCase;

class DashboardTest extends TestCase
{
    /**
     * @test
     */
    public function is_dashboard_loaded()
    {
        $response = $this->get('/dashboard');

        $response->assertSuccessful();
        $response->assertSee('Dashboard');
    }
}
