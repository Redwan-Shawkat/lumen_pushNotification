<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//? Importing the Model
use App\Models\Notification;

use App\Services\FirebaseService;

use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    //? Property to store firebase service instance
    protected $firebaseService;


    public function __construct(FirebaseService $firebaseService)
    {
        //? Inject Firebase
        $this->firebaseService = $firebaseService;
    }

    //? Send Push Notifications
    public function sendPushNotification(Request $request)
    {

        /*
        //? Validation
        $request->validate([
            'device_token' => 'required|string',
            'phone_number' => 'required|string',
            'text' => 'required|string'
        ]);
        */

        $validator = Validator::make($request->all(), [
            'device_token' => 'required|string',
            'phone_number' => 'required|string',
            'body' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        //? Extract Values from Request
        $deviceToken = $request->input('device_token'); //? FCM TOKEN
        $phoneNumber = $request->input('phone_number'); //? Phone Number
        $body = $request->input('body'); //? Text to be displayed


        /*
        //? Send Push Notification using Firebase Service
        try {
            $messageId = $this->firebaseService->sendNotification($deviceToken, $phoneNumber, $body);
            return response()->json(['success' => true, 'message_id' => $messageId], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
        */

        //? Send Push Notification using Firebase Service and Save it on Database
        try {
            //? Save on the database first
            $notification = Notification::create([
                'phone_number' => $phoneNumber,
                'body' => $body
            ]);

            //? Send Push Notification using Firebase Service
            $messageId = $this->firebaseService->sendNotification($deviceToken, $phoneNumber, $body);

            return response()->json([
                'success' => true,
                'meesage_id' => $messageId,
                'saved_notification_id' => $notification->id //? Rreturn Saved Record ID
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
