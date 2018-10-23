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

                $this->dropUnusedOnBoardingPercentageData()
                    ->addMissingOnBoardingPercentageData()
                    ->calculateTotalCountPerWeeklyStep()
                    ->fetchWeeklyPercentagesPerStep();

                return [
                    'name' => 'Week ' . $key,
                    'data' => $this->getWeeklyData()
                ];
            })->values();
    }

    public function setWeeklyData(Collection $weeklyData)
    {
        $this->weeklyData = $weeklyData;
    }

    public function getWeeklyData(): Collection
    {
        return $this->weeklyData;
    }

    public function dropUnusedOnBoardingPercentageData(): ActivityRepository
    {
        $this->weeklyData = $this->weeklyData->dropUnusedOnBoardingPercentageData($this->percentages);

        return $this;
    }

    public function addMissingOnBoardingPercentageData(): ActivityRepository
    {
        $missingPercentages = collect($this->percentages)->diff($this->weeklyData->pluck('onboarding_percentage'));

        $missingPercentages->each(function ($percentage) {

            $stepData = new Activity();
            $stepData->onboarding_percentage = $percentage;
            $stepData->week = $this->weeklyData->first()['week'];
            $stepData->count = 0;

            $this->weeklyData->push($stepData);
        });

        $this->weeklyData = $this->weeklyData->sortBy('onboarding_percentage')->values();

        return $this;
    }

    public function calculateTotalCountPerWeeklyStep(): ActivityRepository
    {
        $this->weeklyData = $this->weeklyData->calculateTotalCountPerWeeklyStep();

        return $this;
    }

     public function calculatePercentage($stepCount, $weeklyCount): float
     {
         return round(($stepCount / $weeklyCount) * 100);
     }

     private function fetchWeeklyPercentagesPerStep(): ActivityRepository
     {
         $this->weeklyData = $this->weeklyData->map(function ($stepData) {
             return $this->calculatePercentage($stepData->totalCount, $this->weeklyData->sum('count'));
         });

         return $this;
     }
}
