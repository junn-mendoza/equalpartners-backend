<?php

namespace App\Services;

use App\Http\Resources\RewardGetResource;
use Exception;
use App\Models\Reward;
use App\Models\UserReward;
//use App\Models\UserReward;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RewardService extends Common
{
    public function get($place_id)
    {

        $reward = Reward::with('user_rewards.user')->where('place_id', $place_id)
            ->get();
        //return response()->json($reward, 200);
        return response()->json(RewardGetResource::collection($reward), 200);
    }
    public function add($data)
    {
        //dd($data['place_id']);
        DB::beginTransaction();
        try {
            $reward = Reward::create([
                'place_id' => $data['place_id'],
                'description' => $data['description'],
            ]);
            $this->addAssignee(UserReward::class, $data, $reward->id);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Module (RewardService) add - " . $e->getMessage());
            return response()->json('Something went wrong.' . $e->getMessage(), 400);
        }
        return response()->json('Reward created.', 201);
    }
}
