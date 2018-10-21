<?php declare(strict_types=1);

namespace App\Repositories;

use App\Activity;
use Illuminate\Support\Collection;

class ActivityRepository
{
    /** @var array */
    protected $percentages;

    public function __construct()
    {
        $this->percentages = config('temper.onboarding_percentages');
    }

    public function fetchWeeklyRetention(): Collection
    {
        return Activity::weekly()
            ->get()
            ->groupBy('week')
            ->map(function ($weeklyData, $key) {
                $weeklyData = $this->calculateTotalCountPerStep($this->addMissingPercentages($this->filterOnboardingPercentages($weeklyData)));

                return [
                    'name' => 'Week ' . $key,
                    'data' => $this->fetchWeeklyPercentagesPerStep($weeklyData)
                ];
            })->values();
    }

    public function filterOnboardingPercentages(Collection $weeklyData): Collection
    {
        return $weeklyData->filter(function ($stepData) {
            return (in_array($stepData['onboarding_percentage'], $this->percentages));
        });
    }

    public function addMissingPercentages(Collection $weeklyData): Collection
    {
        $missingPercentages = collect($this->percentages)->diff($weeklyData->pluck('onboarding_percentage'));

        $missingPercentages->each(function ($percentage) use ($weeklyData) {

            $stepData = new Activity();
            $stepData->onboarding_percentage = $percentage;
            $stepData->week = $weeklyData->first()['week'];
            $stepData->count = 0;

            $weeklyData->push($stepData);
        });

        return $weeklyData->sortBy('onboarding_percentage')->values();
    }

    public function calculateTotalCountPerStep(Collection $weeklyData): Collection
    {
        return $weeklyData->map(function ($stepData) use ($weeklyData) {
            $stepData['totalCount'] = $weeklyData
                    ->where('onboarding_percentage', '>', $stepData['onboarding_percentage'])
                    ->sum('count') + $stepData['count'];

            return $stepData;
        })->values();
    }

     public function calculatePercentage($stepCount, $weeklyCount): float
     {
         return round(($stepCount / $weeklyCount) * 100);
     }

     private function fetchWeeklyPercentagesPerStep($weeklyData): Collection
     {
         return $weeklyData->map(function ($stepData) use ($weeklyData) {
             return $this->calculatePercentage($stepData->totalCount, $weeklyData->sum('count'));
         });
     }
}