<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskCategory extends Model
{
    use HasFactory;
    protected $guarded = ['id', 'created_at', 'updated_at'];
    public function addCategoryToTask($taskId, $categoryId, $customName = null, $color = null)
    {
        $task = Task::find($taskId);
        $category = Category::find($categoryId);

        // Check if the category exists
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        // Check if the category is "Custom"
        if ($category->name === 'Custom') {
            // Allow multiple "Custom" categories to be added
            $task->categories()->attach($categoryId, ['custom_name' => $customName, 'color' => $color]);
        } else {
            // For non-"Custom" categories, check if it's already added
            $existingCategory = $task->categories()->where('category_id', $categoryId)->first();

            if ($existingCategory) {
                return response()->json(['error' => 'This category is already associated with the task'], 400);
            } else {
                $task->categories()->attach($categoryId, ['custom_name' => $customName, 'color' => $color]);
            }
        }

        return response()->json(['success' => 'Category added to task'], 200);
    }
}
