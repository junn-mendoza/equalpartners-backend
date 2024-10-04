<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RewardService;
use App\Http\Requests\RewardAddRequest;
use App\Http\Requests\RewardDeleteRequest;
use App\Http\Requests\RewardGetRequest;

class RewardController extends Controller
{
    protected RewardService $rewardService;
    public function __construct(RewardService $rewardService)
    {
        $this->rewardService = $rewardService;
    }
    public function add(RewardAddRequest $request)
    {
        return $this->rewardService->add($request->validated());
    }
    public function delete(RewardDeleteRequest $request)
    {
        return $this->rewardService->delete($request->validated());
    }

    public function get_reward($place_id)
    {
        return $this->rewardService->get($place_id);
    }

    public function get_reward_id($place_id, $id)
    {
        return $this->rewardService->get_id($place_id, $id);
    }
}
