<?php declare(strict_types=1);

namespace App\Repositories;

use App\Activity;
use Illuminate\Support\Collection;

class ActivityRepository
{
    public function fetchWeeklyRetention(): Collection
    {
        return Activity::weekly()
            ->get()
            ->groupBy('week')
            ->map(function ($weeklyData, $key) {
                return [
                    'name' => 'Week ' . $key,
                    'data' => $this->fetchWeeklyPercentagesPerStep($this->calculateTotalCountPerStep($weeklyData))
                ];
            })->values();
    }

    public function calculatePercentage($stepCount, $weeklyCount): float
    {
        return round(($stepCount / $weeklyCount) * 100);
    }

    public function calculateTotalCountPerStep($weeklyData): Collection
    {
        return $weeklyData->map(function ($stepData, $key) use ($weeklyData) {
            $stepData['totalCount'] = ($weeklyData->sum('count') - $weeklyData->take($key)->sum('count'));

            return $stepData;
        });
    }

    private function fetchWeeklyPercentagesPerStep($weeklyData): Collection
    {
        return $weeklyData->map(function ($stepData) use ($weeklyData) {
            return $this->calculatePercentage($stepData->totalCount, $weeklyData->sum('count'));
        });
    }
}