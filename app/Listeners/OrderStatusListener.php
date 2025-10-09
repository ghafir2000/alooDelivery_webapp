<?php

namespace App\Listeners;

use Mockery\Matcher\Not;
use App\Events\OrderStatusEvent;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;
use App\Traits\PushNotificationTrait;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderStatusListener
{

    use PushNotificationTrait;
    use InteractsWithQueue;

    protected $whatsAppService;

    /**
     * Create the event listener.
     */
    public function __construct( NotificationService $whatsAppService)
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
            // Fetch the settings from the database
            $feedbackStatus = getWebConfig(name: 'whatsapp_feedback_status');
            $rawFeedbackMessage = getWebConfig(name: 'whatsapp_feedback_message');

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
