<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TaskListingService
{
    // Generates a header for the task date (e.g., "Today", "Tomorrow", or actual date)
    public static function getHeaderForDate(Carbon $dueDate, Carbon $currentDate)
    {
        if ($dueDate->isSameDay($currentDate)) {
            return 'Today';
        } elseif ($dueDate->isSameDay($currentDate->copy()->addDay(1))) {
            return 'Tomorrow';
        } else {
            return $dueDate->format('l j F'); // Example: "Wednesday 25 September"
        }
    }

    // Calculates the due date based on the frequency and timeframe
    public static function calculateDueDate($frequency, $timeframe)
    {
        $currentDate = Carbon::now(); // Get the current date

        if ($timeframe === 'weekly') {
            // Get the start of the current week (Sunday) and add frequency days
            return $currentDate->startOfWeek(Carbon::SUNDAY)->addDays($frequency);
        } elseif ($timeframe === 'monthly') {
            // Ensure the frequency is within the valid range for monthly (1-31)
            $frequency = max(1, min(31, $frequency));
            return Carbon::createFromDate($currentDate->year, $currentDate->month, $frequency);
        }

        throw new \Exception('Invalid timeframe');
    }

    // Helper function to generate ordinal numbers (1st, 2nd, 3rd, etc.)
    public static function ordinal($number)
    {
        $suffixes = ['th', 'st', 'nd', 'rd'];
        $value = $number % 100;

        return $number . ($suffixes[($value - 20) % 10] ?? $suffixes[$value] ?? $suffixes[0]);
    }

    // Generates readable text for the repeat and frequency (e.g., "Every 2nd week on Tuesday, Friday")
    public static function generateDateText($repeat, $timeframe, $frequencies)
    {
        $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        
        $dateText = 'Every ' . self::ordinal($repeat) . ' ' . ($timeframe == 'weekly' ? 'week' : 'month');

        if ($timeframe === 'weekly') {
            $days = array_map(fn($frequency) => $daysOfWeek[$frequency], $frequencies);
            $dateText .= ' on ' . implode(', ', $days);
        } elseif ($timeframe === 'monthly') {
            $days = array_map(fn($frequency) => self::ordinal($frequency) . ' day', $frequencies);
            $dateText .= ' on ' . implode(', ', $days);
        }

        return $dateText;
    }

    // Helper function to add a task to the output array
    private function addTaskToOutput(&$output, $task, $user, $dueDate, $header, $dataText)
    {
        $min = ($task->min === 0?'':$task->min.($task->min>1?' mins':' min'));
        $hr = ($task->hr === 0?'':$task->hr.($task->hr>1?' hrs ':' hr '));
        $output[$header][] = [
            'duedate' => $dueDate->format('Y-m-d'),
            'name' => $task->name,
            'timeframe' => $task->timeframe,
            'user_name' => $user->name,
            'category' => $task->categories[0]->name,
            'icon' => $task->categories[0]->icon,
            'color' => $task->categories[0]->color,
            'image' => $user->profile,
            'repeat' => $task->repeat,
            'data_text' => $dataText,
            'time' => $hr.$min,
            'auth_id' => Auth::id(),
            'user_id' => $user->id,
            'task_id' => $task->id,
        ];
    }

    // Main function to build the task list
   private function isUser($users, $auth_id) 
   {
        // dump($auth_id);
        // dd($users ?? []);
        foreach($users ?? [] as $user) {
            if($auth_id == $user->id) {
                return true;
            }
        }
        return false;
    }
    public function buildTask($tasks)
    {
        $output = [];
        $currentDate = Carbon::now()->startOfDay(); // Starting from today
        $id = Auth::id();
        
        foreach ($tasks as $task) {
            $isUser = $this->isUser($task->users, $id);
            //if($isUser) {
                foreach ($task->users as $user) {
                    
                    if ($task->timeframe != null) {
                        $isFrequent = false;
                        foreach ($task->frequencies as $frequency) {
                            $isFrequent = true;    
                            $dueDate = self::calculateDueDate($frequency->frequent, $task->timeframe);
                            $dueDateCarbon = Carbon::parse($dueDate);
                            // dump('$dueDateCarbon',$dueDateCarbon);
                            // dump('$currentDate',$currentDate);
                            // dd('between',$dueDateCarbon->between($currentDate, $currentDate->copy()->addDays(7)));
                            if ($dueDateCarbon->between($currentDate, $currentDate->copy()->addDays(7))) {
                                
                                $header = self::getHeaderForDate($dueDateCarbon, $currentDate);
                                $dataText = self::generateDateText($task->repeat, $task->timeframe, $task->frequencies->pluck('frequent')->toArray());
                                $this->addTaskToOutput($output, $task, $user, $dueDateCarbon, $header, $dataText);
                            }
                        }
                        if(!$isFrequent) {
                            $dueDateCarbon = Carbon::parse($task->duedate);
                            if ($dueDateCarbon->between($currentDate, $currentDate->copy()->addDays(7))) {
                                $header = self::getHeaderForDate($dueDateCarbon, $currentDate);
                                $this->addTaskToOutput($output, $task, $user, $dueDateCarbon, $header, $dataText='');
                            }
                        }
                    } else {
                        
                        // Task has no timeframe, use the task's own due date and leave data_text blank
                        $dueDateCarbon = Carbon::parse($task->duedate);

                        if ($dueDateCarbon->between($currentDate, $currentDate->copy()->addDays(7))) {
                            $header = self::getHeaderForDate($dueDateCarbon, $currentDate);
                            $this->addTaskToOutput($output, $task, $user, $dueDateCarbon, $header, '');
                            
                            
                        }
                    }
                }
            //}
        }
        //dd();
        // Add "No tasks available" message for empty days
        for ($i = 0; $i <= 7; $i++) {
            $date = $currentDate->copy()->addDays($i);
            $header = self::getHeaderForDate($date, $currentDate);
            if (!isset($output[$header])) {
                $output[$header][] = ['name' => 'No tasks available.'];
            }
        }

        return $output;
    }

    

    public function sortTasksByDate($tasks)
    {
        $currentDate = Carbon::now(); // Starting point for "Today"
        $sortedTasks = [];

        // Loop through the tasks and prepare them for sorting
        foreach ($tasks as $header => $taskList) {
            if ($header === 'Today') {
                $dueDate = $currentDate->copy(); // Today
            } elseif ($header === 'Tomorrow') {
                $dueDate = $currentDate->copy()->addDay(1); // Tomorrow
            } else {
                // Extract the date from headers like "Wednesday 25 September"
                $dueDate = Carbon::createFromFormat('l j F', $header); // Parse the date from string
            }

            // Add the tasks along with the actual due date for sorting
            $sortedTasks[] = [
                'dueDate' => $dueDate, // Store Carbon date object for sorting
                'header' => $header, // Keep the original header
                'tasks' => $taskList // Task list for that date
            ];
        }

        // Sort tasks by the actual due date
        usort($sortedTasks, function ($a, $b) {
            return $a['dueDate']->greaterThan($b['dueDate']) ? 1 : -1;
        });

        // Prepare the final sorted array in the desired format
        $finalOutput = [];
        foreach ($sortedTasks as $taskData) {
            $finalOutput[$taskData['header']] = $taskData['tasks'];
        }

        return $finalOutput;
    }

}
