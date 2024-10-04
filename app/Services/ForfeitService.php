<?php

namespace App\Services;

use App\Http\Resources\ForfeitGetResource;
use Exception;
use App\Models\Forfeit;
use App\Models\UserReward;
use App\Models\UserForfeit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ForfeitService extends Common
{
    // public function upsert($data)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $reward = Forfeit::updateOrCreate([
    //             'place_id' => $data['place_id'],
    //         ], [
    //             'place_id' => $data['place_id'],
    //             'challenges' => $data['challenges'],
    //             'must_complete' => $data['must_complete'],
    //         ]);
    //         $existingAssignees = UserForfeit::where('id', $reward->id)->pluck('user_id')->toArray();
    //         $newAssignees = array_column($data['assignee'], 'user_id');
    //         UserForfeit::where('reward_id', $reward->id)
    //             ->whereNotIn('user_id', $newAssignees)
    //             ->delete();
    //         foreach ($data['assignee'] as $assignee) {
    //             if (!in_array($assignee['user_id'], $existingAssignees)) {
    //                 UserForfeit::create([
    //                     'reward_id' => $reward->id,
    //                     'user_id' => $assignee['user_id'],
    //                 ]);
    //             }
    //         }
    //         DB::commit();
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         Log::error("Module (RewardService) upsert - " . $e->getMessage());
    //         return response()->json('Something went wrong.' . $e->getMessage(), 400);
    //     }
    //     return response()->json('Reward created.', 201);
    // }

    public function add($data)
    {
        DB::beginTransaction();
        try {
            $forfeit = Forfeit::updateOrCreate(
                [
                    'place_id' => $data['place_id'],
                    'id' => $data['id'],
                ],
                [
                'place_id' => $data['place_id'],
                'must_complete' => $data['must_complete'],
                'challenges' => $data['challenges'],
            ]);
            $this->addAssignee(UserForfeit::class, $data, $forfeit->id);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Module (ForfeitService) add - " . $e->getMessage());
            return response()->json('Something went wrong.' . $e->getMessage(), 400);
        }
        return response()->json('Forfeit created.', 201);
    }

    public function delete($data)
    {
        Forfeit::where('place_id', $data['place_id'])
            ->where('id', $data['id'])
            ->delete();
        return response()->json('Forfeit is successfully deleted.');
    }

    public function get($place_id)
    {
        $forfeit = Forfeit::with('user_forfeits.user')->where('place_id', $place_id)
            ->get();
        return response()->json(ForfeitGetResource::collection($forfeit), 200);
    }

    public function get_id($place_id, $id)
    {

        $forfeit = Forfeit::with('user_forfeits.user')
            ->where('place_id', $place_id)
            ->where('id', $id)
            ->get();
        return response()->json(ForfeitGetResource::collection($forfeit), 200);
    }

}
