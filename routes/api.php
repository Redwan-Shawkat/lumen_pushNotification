<?php

$router->group(['middleware' => 'apiToken'], function () use ($router) {
    // $router->post('/send-message', ['uses' => 'MessageController@store']);
    $router->post('/send-notification', ['uses' => 'NotificationController@sendPushNotification']);
});

$router->get('/', function () {
    return response()->json(['message' => 'Lumen API is running'], 200);
});
