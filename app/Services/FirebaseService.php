<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    //? Property to store Firebase Messaging Insance
    protected $messaging;

    public function __construct()
    {

        //? Firebase Instance using Credentials from .env
        $firebase = (new Factory)->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')));

        //? Initializing Firebase Clouding Messaging
        $this->messaging = $firebase->createMessaging();
    }

    //? Sending Notifications
    public function sendNotification($deviceToken, $title, $body)
    {

        //? Create a new Firebase CloudMessage
        $message = CloudMessage::new() //? Creating a new message instance
            ->toToken($deviceToken) //? Device Token
            ->withNotification(Notification::create($title, $body)); //? Setting Title & Body


        //? Sending the created message with a response
        return $this->messaging->send($message);
    }
}
