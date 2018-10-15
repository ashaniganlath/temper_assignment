<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\ActivityRepository;
use Illuminate\Http\JsonResponse;

class ActivityController extends Controller
{
    /** @var ActivityRepository */
    private $repository;

    public function __construct(ActivityRepository $activityRepository)
    {
        $this->repository = $activityRepository;
    }

    public function __invoke(): JsonResponse
    {
        $data = $this->repository->fetchWeeklyRetention();

        return response()->json($data);
    }
}
