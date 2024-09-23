<?php
namespace App\Services;

use Carbon\Carbon;

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
            'data_text' => $dataText
        ];
    }

    // Main function to build the task list
    public function buildTask($tasks)
    {
        $output = [];
        $currentDate = Carbon::now(); // Starting from today

        foreach ($tasks as $task) {
            foreach ($task->users as $user) {
                if ($task->timeframe != null) {
                    foreach ($task->frequencies as $frequency) {
                        $dueDate = self::calculateDueDate($frequency->frequent, $task->timeframe);
                        $dueDateCarbon = Carbon::parse($dueDate);

                        if ($dueDateCarbon->between($currentDate, $currentDate->copy()->addDays(7))) {
                            $header = self::getHeaderForDate($dueDateCarbon, $currentDate);
                            $dataText = self::generateDateText($task->repeat, $task->timeframe, $task->frequencies->pluck('frequent')->toArray());
                            $this->addTaskToOutput($output, $task, $user, $dueDateCarbon, $header, $dataText);
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
        }

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
}
