<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Services\TaskService;

class SendDailyNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily push notifications to users at 9 AM';

    protected TaskService $taskService;
    public function __construct(TaskService $taskService)
    {
        parent::__construct();
        $this->taskService = $taskService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tasks = $this->taskService->current();
        foreach($tasks as $task) {
            $this->sendPushNotification($task->token, $task->name);
        }
    }

    protected function sendPushNotification($expoPushToken, $message)
    {
        $client = new Client();
        $url = 'https://exp.host/--/api/v2/push/send';

        $response = $client->post($url, [
            'json' => [
                'to' => $expoPushToken,
                'sound' => 'default',
                'body' => $message,
                'data' => ['extraData' => 'data here'],
            ]
        ]);

        $this->info("Notification sent: " . $response->getBody());
    }
}
