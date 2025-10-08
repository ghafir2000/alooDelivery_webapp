<?php

namespace App\Services;

use App\Traits\FileManagerTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    use FileManagerTrait;

     private $whatsappSettings;

    public function __construct()
    {
        try {
            // Find the 'whatsapp' configuration in the addon_settings table
            $config = DB::table('addon_settings')->where('key_name', 'whatsapp')->first();

            // If a configuration is found, decode the JSON from 'live_values'
            // Otherwise, default to an empty array.
            $this->whatsappSettings = $config ? json_decode($config->live_values, true) : [];

        } catch (\Exception $e) {
            Log::error('Could not load WhatsApp settings from database: ' . $e->getMessage());
            $this->whatsappSettings = []; // Ensure it's an array on failure
        }
    }
    public function getNotificationAddData(object $request): array
    {
        $image = $request['image'] ? $this->upload(dir: 'notification/', format: 'webp', image: $request->file('image')) : '';
        return [
            'title' => $request['title'],
            'description' => $request['description'],
            'image' => $image,
            'notification_count' => 1
        ];
    }
    public function getNotificationUpdateData(object $request, string|null $notificationImage): array
    {
        $image = $request['image'] ? $this->update(dir: 'notification/', oldImage: $notificationImage, format: 'webp', image: $request->file('image')) : $notificationImage;
        return [
            'title' => $request['title'],
            'description' => $request['description'],
            'image' => $image,
        ];
    }



    
   public function sendWhatsAppNotification($number, $postData)
    {
        // First, check if the WhatsApp gateway is enabled in the settings
        if (empty($this->whatsappSettings) || !isset($this->whatsappSettings['status']) || $this->whatsappSettings['status'] != 1) {
            Log::warning('WhatsApp notification sending is disabled in settings.');
            return; // Stop execution if not enabled
        }
        $sanitizedNumber = preg_replace('/[^0-9]/', '', $number);
        Log::info('Attempting to send WhatsApp notification to sanatized number: ' . $sanitizedNumber);

        // Add a check to ensure the message isn't empty after trimming
         $rawMessage = $postData['description'] ?? '';

        // +++ ADD THIS BLOCK TO SANITIZE AND VALIDATE THE MESSAGE +++
        $message = trim($rawMessage); // This removes all leading/trailing whitespace
        if (empty($message)) {
            Log::error('WhatsApp notification failed: Message was empty after trimming.', ['raw' => $rawMessage]);
            return; // Stop execution to prevent sending an invalid request
        }
        try {
            $response = Http::asForm()
                ->timeout(15)
                // USE THE DATABASE VALUES INSTEAD OF ENV()
                ->post($this->whatsappSettings['api_url'], [
                    'appkey' => $this->whatsappSettings['app_key'],
                    'authkey' => $this->whatsappSettings['auth_key'],
                    'to' => $sanitizedNumber,
                    'message' => $message,
                    'sandbox' => 'false',
                ]);

            if ($response->failed()) {
                Log::error('WhatsApp API request failed.', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            } else {
                Log::info('WhatsApp Notification Sent Successfully.', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('WhatsApp API Connection Exception: ' . $e->getMessage());
        }
    
    }
}
