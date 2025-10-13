<?php

namespace App\Listeners;

use Mockery\Matcher\Not;
use App\Events\NewUserEvent;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Repositories\BusinessSettingRepositoryInterface;

class RegisterListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected NotificationService $whatsAppService;

    /**
     * Create the event listener.
     */
    public function __construct(NotificationService $whatsAppService,
        private readonly BusinessSettingRepositoryInterface $businessSettingRepo,
                                )
    {
                logger("on listener for new user constructor ");

        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Handle the event.
     */
    public function handle(NewUserEvent $event): void
    {
                logger("on listener for new user ");

        $user = $event->user;
        $customerNumber = $user->phone; // Assuming the phone number is on the user model

        // 1. Fetch the single 'welcome_settings' row from the database.
        $welcomeSettings = $this->businessSettingRepo->getFirstWhere(params: ['type' => 'welcome_settings']);

        // 2. Decode the JSON value. If it doesn't exist or is invalid, default to an empty array.
        $settingsData = json_decode($welcomeSettings->value,true) ?: [];

        // 3. Extract the status and message from the decoded array, providing default values.
        $welcomeStatus = $settingsData['status']?? 0; // Default to 0 (off)
        $welcomeMessage = $settingsData['message']?? ''; // Default to an empty string

        logger("Welcome status: $welcomeStatus, Welcome message: $welcomeMessage");
        if ($welcomeStatus == 1 && !empty($welcomeMessage)) {
            $postData = [
                'description' => str_replace('#USER_FIRST_NAME#', $user->f_name, $welcomeMessage)
            ];

            Log::info("Queueing welcome message for new user #{$user->id}.");

            // Call your existing service method
            $this->whatsAppService->sendWhatsAppNotification($customerNumber, $postData);
        }
    }
}
