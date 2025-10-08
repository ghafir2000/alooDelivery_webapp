<?php

namespace App\Listeners;

use App\Events\OrderPlacedEvent;
use App\Traits\EmailTemplateTrait;
use App\Traits\PushNotificationTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class OrderPlacedListener
{
    use PushNotificationTrait, EmailTemplateTrait;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPlacedEvent $event): void
    {
        if ($event->email) {
            $this->sendMail($event);
        }
        if ($event->notification) {
            $this->sendNotification($event);
        }

    }

    private function sendMail(OrderPlacedEvent $event): void
    {
        $email = $event->email;
        $data = $event->data;
        try {
            $this->sendingMail(sendMailTo: $email, userType: $data['userType'], templateName: $data['templateName'], data: $data);
        } catch (\Exception $exception) {

        }
    }

    private function sendNotification(OrderPlacedEvent $event): void
    {
        $key = $event->notification->key;
        $type = $event->notification->type;
        $order = $event->notification->order;
        $this->sendOrderNotification(key: $key, type: $type, order: $order);
    }

     public function sendWhatsAppNotification($number, $title)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL =>  env('CURLOPT_URL'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'appkey' => env('WHATSAPP_APP_KEY'),
                'authkey' =>  env('WHATSAPP_AUTH_KEY'),
                'to' => '0992615866',
                'message' => 'Example message',
                'sandbox' => 'false'
            ),
        ));

        $response = curl_exec($curl);

        if ($response === false) {
            echo 'cURL Error: ' . curl_error($curl);
        } else {
            echo $response;
        }

        curl_close($curl);
    }
}
