<?php declare(strict_types=1);

namespace App\Repositories;

use App\Activity;
use Illuminate\Support\Collection;

class ActivityRepository
{
    /** @var array */
    protected $percentages;

    /** @var Collection */
    public $weeklyData;

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
                $this->setWeeklyData($weeklyData);

                $this->weeklyData = $this->dropUnusedOnBoardingPercentageData();
                $this->weeklyData = $this->addMissingPercentages();
                $this->weeklyData = $this->calculateTotalCountPerStep();

                return [
                    'name' => 'Week ' . $key,
                    'data' => $this->fetchWeeklyPercentagesPerStep()
                ];
            })->values();
    }

    public function setWeeklyData(Collection $weeklyData)
    {
        $this->weeklyData = $weeklyData;
    }

    public function dropUnusedOnBoardingPercentageData(): Collection
    {
        return $this->weeklyData->dropUnusedOnBoardingPercentageData($this->percentages);
    }

    public function addMissingPercentages(): Collection
    {
        $missingPercentages = collect($this->percentages)->diff($this->weeklyData->pluck('onboarding_percentage'));

        $missingPercentages->each(function ($percentage) {

            $stepData = new Activity();
            $stepData->onboarding_percentage = $percentage;
            $stepData->week = $this->weeklyData->first()['week'];
            $stepData->count = 0;

            $this->weeklyData->push($stepData);
        });

        return $this->weeklyData->sortBy('onboarding_percentage')->values();
    }

    public function calculateTotalCountPerStep(): Collection
    {
        return $this->weeklyData->calculateTotalCountPerStep();
    }

     public function calculatePercentage($stepCount, $weeklyCount): float
     {
         return round(($stepCount / $weeklyCount) * 100);
     }

     private function fetchWeeklyPercentagesPerStep(): Collection
     {
         return $this->weeklyData->map(function ($stepData) {
             return $this->calculatePercentage($stepData->totalCount, $this->weeklyData->sum('count'));
         });
     }
}
