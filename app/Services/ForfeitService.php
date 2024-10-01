<?php

namespace App\Services;

use Exception;
use App\Models\Forfeit;
use App\Models\UserReward;
use App\Models\UserForfeit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ForfeitService
{
    public function upsert($data)
    {
        DB::beginTransaction();
        try {
            $reward = Forfeit::updateOrCreate([
                'place_id' => $data['place_id'],
            ], [
                'place_id' => $data['place_id'],
                'challenges' => $data['challenges'],
                'must_complete' => $data['must_complete'],
            ]);
            $existingAssignees = UserForfeit::where('id', $reward->id)->pluck('user_id')->toArray();
            $newAssignees = array_column($data['assignee'], 'user_id');
            UserForfeit::where('reward_id', $reward->id)
                ->whereNotIn('user_id', $newAssignees)
                ->delete();
            foreach ($data['assignee'] as $assignee) {
                if (!in_array($assignee['user_id'], $existingAssignees)) {
                    UserForfeit::create([
                        'reward_id' => $reward->id,
                        'user_id' => $assignee['user_id'],
                    ]);
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Module (RewardService) upsert - " . $e->getMessage());
            return response()->json('Something went wrong.' . $e->getMessage(), 400);
        }
        return response()->json('Reward created.', 201);
    }
}
