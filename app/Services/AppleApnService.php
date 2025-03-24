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
            'certificate_path' => storage_path(env('APN_CERT')),
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

            foreach ($responses as $response) {
                if ($response->getStatusCode() === 200) {
                    return $response->getApnsId();
                } else {
                    Log::error('APN Error:' . $response->getReasonPhrase());
                    throw new \Exception($response->getReasonPhrase());
                }
            }
        } catch (\Exception $e) {
            Log::error('APN Oush Failed:' . $e->getMessage());
        }
    }
}
