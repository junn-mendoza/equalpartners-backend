<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RewardService;
use App\Http\Requests\RewardAddRequest;

class ForfeitController extends Controller
{
    protected RewardService $rewardService;
    public function __construct(RewardService $rewardService)
    {
        $this->rewardService = $rewardService;
    }
    // public function add(RewardAddRequest $request)
    // {
    //     return $this->rewardService->upsert($request->validated());
    // }
}
