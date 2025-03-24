<?php

namespace App\Services;


//? Logging the Error Messages
use Illuminate\Support\Facades\Log;
//? Used to create a connection to Applie APNs
use Pushok\Client;
//? Authenticates with APNs (Deprecated)
////use Pushok\AuthProvider;
use Pushok\AuthProvider\Certificate;
//? Represents a single APNs notification
use Pushok\Notification;
//? Represents the payload of a notification
use Pushok\Payload;

class AppleApnService
{
    protected $authProvider;

    public function __construct()
    {

        //? APNs authentication with .pem certificate
        $options = [
            'certificate_path' => base_path(env('APN_CERT')),
            'certificate_secret' => env('APN_PASSPHRASE', '')
        ];

        //// $this->authProvider = AuthProvider::create($options);
        //? [Deprecated]
        $this->authProvider = Certificate::create($options);
    }

    public function sendNotification($deviceToken, $body)
    {
        //? CREATE A PUSH NOTIFICATION PAYLOAD
        $payload = Payload::create()->setAlert($body);
        $notification = new Notification($payload, $deviceToken);

        //? CONNECT TO APNs (sandboc or production)
        $client = new Client($this->authProvider, env('APNS_ENVIRONMENT', 'sandbox'));
        $client->addNotification($notification);

        try {
            //? SEND THE NOTIFICATION
            $responses = $client->push();

            if (empty($responses)) {
                Log::error('APN Error: No response from Apple servers.');
                throw new \Exception('No response from Apple servers.');
            }

            foreach ($responses as $response) {
                if ($response->getStatusCode() === 200) {
                    return $response->getApnsId();
                }
            }

            Log::error('APN Error: All push attempts failed.');
            throw new \Exception('Failed to send APN notification.');
        } catch (\Exception $e) {
            Log::error('APN Oush Failed:' . $e->getMessage());
        }
    }
}
