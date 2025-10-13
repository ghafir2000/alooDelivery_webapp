<?php

namespace App\Listeners;

use Mockery\Matcher\Not;
use App\Events\OrderStatusEvent;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;
use App\Traits\PushNotificationTrait;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Repositories\BusinessSettingRepositoryInterface;

class OrderStatusListener
{

    use PushNotificationTrait;
    use InteractsWithQueue;

    protected $whatsAppService;

    /**
     * Create the event listener.
     */
    public function __construct( NotificationService $whatsAppService,
    private readonly BusinessSettingRepositoryInterface $businessSettingRepo,)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Handle the event.
     */
    public function handle(OrderStatusEvent $event): void
    {
        $this->sendNotification($event);
        logger("Order status changed to  . $event->key  on the call of the event type :  $event->type");

        if ($event->key === 'delivered' && $event->type === 'customer') {
             // 1. Fetch the single 'feedback_settings' row from the database.
            $feedbackSettings = $this->businessSettingRepo->getFirstWhere(params: ['type' => 'feedback_settings']);

            
            // 2. Decode the JSON value. If it doesn't exist or is invalid, default to an empty array.
            $settingsData = json_decode($feedbackSettings->value,true) ?: [];
            // dd($settingsData['status']);

            // 3. Extract the status and message from the decoded array, providing default values.
            $feedbackStatus = $settingsData['status']?? 0; // Default to 0 (off)
            $rawFeedbackMessage = $settingsData['message']?? ''; // Default to an empty string
        
            // ONLY proceed if the feature is enabled and the message is not empty
            if (isset($feedbackStatus) && $feedbackStatus == 1 && !empty($rawFeedbackMessage)) {
                Log::info("WhatsApp Feedback feature is enabled. Preparing message for order #{$event->order->id}.");

                // ... your logic to get customer number ...
                $customerNumber = $event->order->customer->phone;
                
                $strippedMessage = strip_tags($rawFeedbackMessage);
                $feedbackMessage = html_entity_decode($strippedMessage);
                
                // Replace a placeholder like #ORDER_ID# with the actual order ID
                $finalMessage = str_replace('#ORDER_ID#', $event->order->id, $feedbackMessage);

                $postData = ['description' => $finalMessage];
        
                $this->whatsAppService->sendWhatsAppNotification($customerNumber, $postData);
            } else {
                Log::info("WhatsApp Feedback feature is disabled in settings. Skipping for order #{$event->order->id}.");
            }
        }
    }

    private function sendNotification(OrderStatusEvent $event): void
    {
        $key = $event->key;
        $type = $event->type;
        $order = $event->order;
        $this->sendOrderNotification(key: $key, type: $type, order: $order);
    }

}
