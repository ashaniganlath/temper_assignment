<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activity_log';

    protected $fillable = [
        'user_id',
        'onboarding_percentage',
    ];

    public function scopeWeekly(Builder $query)
    {
        $query->selectRaw('*, COUNT(*) as count')
            ->fromSub('(SELECT onboarding_percentage, WEEK(created_at, 1) as week FROM activity_log)', 'w')
            ->groupBy(['w.week', 'w.onboarding_percentage'])
            ->orderBy('w.week')
            ->orderBy('w.onboarding_percentage');
    }
}
