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
        // LOG: Log the very beginning of the function call with all initial data.
        Log::info('--- Starting WhatsApp Notification Process ---', [
            'original_number' => $number,
            'post_data' => $postData
        ]);

        // First, check if the WhatsApp gateway is enabled in the settings
        if (empty($this->whatsappSettings) || !isset($this->whatsappSettings['status']) || $this->whatsappSettings['status'] != 1) {
            // LOG: Log the reason for stopping, and dump the settings that were checked.
            Log::warning('WhatsApp notification sending is disabled or settings are invalid. Halting execution.', [
                'whatsapp_settings_checked' => $this->whatsappSettings
            ]);
            return; // Stop execution if not enabled
        }

        // LOG: Log the settings that will be used for the API call to confirm they are correct.
        Log::info('WhatsApp gateway is enabled. Using the following settings:', $this->whatsappSettings);
        
        $sanitizedNumber = preg_replace('/[^0-9]/', '', $number);
        // LOG: Log the number before and after sanitization.
        Log::info('Sanitizing receiver number.', [
            'original' => $number,
            'sanitized' => $sanitizedNumber
        ]);

        // Add a check to ensure the message isn't empty after trimming
        $rawMessage = $postData['description'] ?? '';

        // +++ ADD THIS BLOCK TO SANITIZE AND VALIDATE THE MESSAGE +++
        $message = trim($rawMessage); // This removes all leading/trailing whitespace
        if (empty($message)) {
            Log::error('WhatsApp notification failed: Message was empty after trimming. Halting execution.', [
                'raw_message' => $rawMessage
            ]);
            return; // Stop execution to prevent sending an invalid request
        }
        
        // LOG: Log the final message content that will be sent.
        Log::info('Message content prepared successfully.', [
            'raw_message' => $rawMessage,
            'final_message_to_send' => $message
        ]);

        try {
            // --- MODIFICATION START ---
            // Clean the API credentials to remove any whitespace or non-printable characters
            
            // 1. Get the raw values from your settings
            $rawAppKey = $this->whatsappSettings['app_key'] ?? null;
            $rawAuthKey = $this->whatsappSettings['auth_key'] ?? null;

            // 2. Trim whitespace from the beginning and end of the strings
            $trimmedAppKey = trim($rawAppKey);
            $trimmedAuthKey = trim($rawAuthKey);

            // 3. (Optional but Recommended) Use regex to remove all non-printable control characters
            // This regex will keep all printable characters and remove invisible ones like null characters.
            $cleanAppKey = preg_replace('/[[:cntrl:]]/', '', $trimmedAppKey);
            $cleanAuthKey = preg_replace('/[[:cntrl:]]/', '', $trimmedAuthKey);

            // LOG: Log the keys before and after cleaning to verify the process
            Log::info('Sanitizing API credentials.', [
                'raw_app_key' => $rawAppKey,
                'cleaned_app_key' => $cleanAppKey,
                'raw_auth_key' => $rawAuthKey,
                'cleaned_auth_key' => $cleanAuthKey
            ]);
            // --- MODIFICATION END ---


            // LOG: THIS IS THE MOST IMPORTANT LOG. It shows the exact data being sent in the API request.
            // This will confirm if you are using 'appkey' vs 'app_key' correctly.
            $payload = [
                'appkey'  => $cleanAppKey,  // Use the cleaned key
                'authkey' => $cleanAuthKey, // Use the cleaned key
                'to'      => $sanitizedNumber,
                'message' => $message,
                'sandbox' => 'false',
            ];

            Log::info('Making POST request to WhatsApp API with the following data:', [
                'api_url' => $this->whatsappSettings['api_url'],
                'payload' => $payload
            ]);

            $response = Http::asForm()
                ->timeout(15)
                // USE THE DATABASE VALUES INSTEAD OF ENV()
                ->post($this->whatsappSettings['api_url'], $payload); // Use the prepared payload

            if ($response->failed()) {
                // LOG: Enhanced error log to include the request payload for easier debugging.
                Log::error('WhatsApp API request failed.', [
                    'status'        => $response->status(),
                    'response_body' => $response->body(),
                    'data_sent'     => $payload // Include what you sent
                ]);
            } else {
                Log::info('WhatsApp Notification Sent Successfully.', [
                    'status'        => $response->status(),
                    'response_body' => $response->body()
                ]);
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('WhatsApp API Connection Exception: Could not connect to the server.', [
                'error_message' => $e->getMessage(),
                'api_url_tried' => $this->whatsappSettings['api_url']
            ]);
        } catch (\Exception $e) {
            // LOG: A general catch-all for any other unexpected errors.
            Log::error('An unexpected error occurred during the WhatsApp notification process.', [
                'error_class'   => get_class($e),
                'error_message' => $e->getMessage(),
                'trace'         => $e->getTraceAsString() // Full stack trace for deep debugging
            ]);
        }
    }
}
