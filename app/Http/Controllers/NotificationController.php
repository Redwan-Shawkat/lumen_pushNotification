<?php

namespace App\Http\Controllers;

//? Handles IIncoming API Requests
use Illuminate\Http\Request;

//? Importing the Model
use App\Models\Notification;

//? Firebase
use App\Services\FirebaseService;

//? Apple
use App\Services\AppleApnService;

//? Validation for Incoming Requests
use Illuminate\Support\Facades\Validator;

//? Log File
use Illuminate\Support\Facades\Log;

// //? DB FILE
// use Illuminate\Support\Facades\DB;


class NotificationController extends Controller
{
    //? Property to store firebase service instance
    protected $firebaseService;

    //? Property to store apple service instance
    protected $appleApnService;


    // //?Testing DATAVASE CONNECTION
    // public function testDatabaseConnection()
    // {
    //     try {
    //         DB::connection()->getPdo();
    //         return response()->json(['success' => true, 'message' => 'Database connection successful']);
    //     } catch (\Exception $e) {
    //         return response()->json(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    //     }
    // }


    public function __construct(FirebaseService $firebaseService, AppleApnService $appleApnService)
    {
        //? Initialize firebase service
        $this->firebaseService = $firebaseService;
        //? Initialize apple service
        $this->appleApnService = $appleApnService;
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
            'body' => 'required|string',
            'platform' => 'required|string|in:android,ios'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        //? Extract Values from Request
        $deviceToken = $request->input('device_token'); //? FCM TOKEN
        $phoneNumber = $request->input('phone_number'); //? Phone Number
        $body = $request->input('body'); //? Text to be displayed
        $platform = $request->input('platform');; //? P;atf


        /*
        //? Send Push Notification using Firebase Service
        try {
            $messageId = $this->firebaseService->sendNotification($deviceToken, $phoneNumber, $body);
            return response()->json(['success' => true, 'message_id' => $messageId], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
        */

        /*
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
           */

        /*
        try {
            if ($platform === 'android') {
                //? Send Push Notification through Firebess
                $messageId = $this->firebaseService->sendNotification($deviceToken, $phoneNumber, $body);
            } else {
                //? Send Push Notification Apple Apple APN
                $messageId = $this->appleApnService->sendNotification($deviceToken, $phoneNumber, $body);
            }

            return response()->json([
                'success' => true,
                'message_id' => $messageId
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
        */

        try {
            if (strtolower($platform) === 'android') {
                //? strtolower always returns lowercase characters
                $messageId = $this->firebaseService->sendNotification($deviceToken, $phoneNumber, $body);
            } elseif (strtolower($platform) === 'ios') {
                $messageId = $this->appleApnService->sendNotification($deviceToken, $phoneNumber, $body);
                Log::info('APN Response: ' . json_encode($messageId));
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid platform. Please use android or ios',
                ], 400);
            }
            return response()->json([
                'success' => true,
                'message_id' => $messageId
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
