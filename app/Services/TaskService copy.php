<?php

namespace App\Services;

use Carbon\Carbon;

class TaskListingService
{

    public function getHeaderForDate($dueDate, $currentDate)
    {
        if ($dueDate->isSameDay($currentDate)) {
            return 'Today';
        } elseif ($dueDate->isSameDay($currentDate->copy()->addDay(1))) {
            return 'Tomorrow';
        } else {
            return $dueDate->format('l j F'); // Example: Wednesday 25 September
        }
    }

    public function calculateDueDate($frequency, $timeframe)
    {
        $currentDate = Carbon::now(); // Get the current date

        if ($timeframe === 'weekly') {
            // Weekly timeframe: frequency is 0-6 (Sunday to Saturday)
            // Get the start of the current week (Sunday)
            $startOfWeek = $currentDate->startOfWeek(Carbon::SUNDAY);

            // Add frequency days to get the due date within the current week
            $dueDate = $startOfWeek->addDays($frequency);
        } elseif ($timeframe === 'monthly') {
            // Monthly timeframe: frequency is 1-31 (days of the month)
            // Ensure the frequency is within the valid range
            $frequency = max(1, min(31, $frequency));

            // Set the due date to the specific day of the current month
            $dueDate = Carbon::createFromDate($currentDate->year, $currentDate->month, $frequency);
        } else {
            throw new \Exception('Invalid timeframe');
        }

        return $dueDate->format('Y-m-d'); // Return the due date in 'Y-m-d' format
    }

    // Helper function to generate ordinal numbers (1st, 2nd, 3rd, etc.)
    public function ordinal($number)
    {
        $suffixes = ['th', 'st', 'nd', 'rd'];
        $value = $number % 100;

        return $number . ($suffixes[($value - 20) % 10] ?? $suffixes[$value] ?? $suffixes[0]);
    }

    public function generateDateText($repeat, $timeframe, $frequencies)
    {
        // Days of the week (0 = Sunday, 1 = Monday, ..., 6 = Saturday)
        $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        $dateText = 'Every ' . ($timeframe == 'weekly' ? '' : $this->ordinal($repeat)) . ' ' . ($timeframe == 'weekly' ? 'week' : 'month');

        if ($timeframe === 'weekly') {
            // For weekly: map frequencies to days of the week
            $days = array_map(function ($frequency) use ($daysOfWeek) {
                return $daysOfWeek[$frequency];
            }, $frequencies);

            $dateText .= ' on ' . implode(', ', $days);
        } elseif ($timeframe === 'monthly') {
            // For monthly: map frequencies to ordinal days (e.g., 1st, 2nd, 3rd, ...)
            $days = array_map(function ($frequency) {
                return $this->ordinal($frequency) . ' day';
            }, $frequencies);

            $dateText .= ' on ' . implode(', ', $days);
        }

        return $dateText;
    }

    public function buildTask($tasks)
    {
        $tmp = [];
        $currentDate = Carbon::now(); // Starting from today

        // Loop through each task and its frequencies
        foreach ($tasks as $task) {
            if ($task->timeframe != null) {
                foreach ($task->frequencies as $frequency) {
                    foreach ($task->users as $user) {
                        // Calculate the due date based on frequency and timeframe
                        $dueDate = $this->calculateDueDate($frequency->frequent, $task->timeframe);
                        $dueDateCarbon = Carbon::parse($dueDate);

                        // Check if the due date is within 7 days from today
                        if ($dueDateCarbon->between($currentDate, $currentDate->copy()->addDays(7))) {
                            // Determine the header for the task (Today, Tomorrow, or specific date)
                            $header = $this->getHeaderForDate($dueDateCarbon, $currentDate);

                            // Add the task to the output array
                            $tmp[$header][] = [
                                'duedate' => $dueDate,
                                'name' => $task->name,
                                'timeframe' => $task->timeframe,
                                'user_name' => $user->name,
                                'category' => $task->categories[0]->name,
                                'icon' => $task->categories[0]->icon,
                                'color' => $task->categories[0]->color,
                                'image' => $user->profile,
                                'repeat' => $task->repeat,
                                'data_text' => $this->generateDateText($task->repeat, $task->timeframe, $task->frequencies->pluck('frequent')->toArray())
                            ];
                        }
                    }
                }
            } else {
                // Task has no timeframe, use $task->duedate and leave data_text as blank
                foreach ($task->users as $user) {
                    $dueDate = $task->duedate; // Use the task's own due date
                    $dueDateCarbon = Carbon::parse($dueDate);

                    // Check if the due date is within 7 days from today
                    if ($dueDateCarbon->between($currentDate, $currentDate->copy()->addDays(7))) {
                        // Determine the header for the task (Today, Tomorrow, or specific date)
                        $header = $this->getHeaderForDate($dueDateCarbon, $currentDate);

                        // Add the task to the output array
                        $tmp[$header][] = [
                            'duedate' => $dueDate,
                            'name' => $task->name,
                            'timeframe' => null, // Timeframe is null
                            'user_name' => $user->name,
                            'category' => $task->categories[0]->name,
                            'icon' => $task->categories[0]->icon,
                            'color' => $task->categories[0]->color,
                            'image' => $user->profile,
                            'repeat' => $task->repeat,
                            'data_text' => '' // Blank data_text when timeframe is null
                        ];
                    }
                }
            }
        }

        // Check if any day has no tasks and add the "No tasks available" message
        for ($i = 0; $i <= 7; $i++) {
            $date = $currentDate->copy()->addDays($i);
            $header = $this->getHeaderForDate($date, $currentDate);
            if (!isset($tmp[$header])) {
                $tmp[$header][] = ['name' => 'No tasks available.'];
            }
        }

        return $tmp;
    }
}
