<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Message;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{

    public function store(Request $request)
    {
        try {
            Log::info('Received request', ['data' => $request->all()]);

            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|string',
                'text' => 'required|string',
                'device_token' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            //Store Data
            $message = Message::create($request->all());
            Log::info('Message stored successfully', ['message' => $message]);

            return response()->json(['success' => true, 'message' => 'Data stored']);
        } catch (\Exception $e) {
            Log::error('Error in store method', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
