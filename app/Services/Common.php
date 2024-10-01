<?php

namespace App\Services;

use Exception;

class Common
{
    public function addAssignee($model, $data, $id)
    {
        foreach ($data['assignee'] as $assignee) {
            $model::create([
                'reward_id' => $id,
                'user_id' => $assignee,
            ]);
        }
    }
}
