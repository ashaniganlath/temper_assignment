<?php

namespace App\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class CollectionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Collection::macro('dropUnusedOnBoardingPercentageData', function ($percentages) {
            return $this->filter(function ($data) use ($percentages) {
                return (in_array($data['onboarding_percentage'], $percentages));
            });
        });

        Collection::macro('calculateTotalCountPerWeeklyStep', function () {
            return $this->map(function ($data) {
                $data['totalCount'] = $this
                        ->where('onboarding_percentage', '>', $data['onboarding_percentage'])
                        ->sum('count') + $data['count'];

                return $data;
            })->values();
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
