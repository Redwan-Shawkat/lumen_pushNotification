<?php

/** @var \Laravel\Lumen\Routing\Router $router */


$router->group(['midlleware' => 'staticToken'], function () use ($router) {
    $router->post('/send-message', ['uses' => 'MessageController@store']);
});

$router->get('/', function () {
    return response()->json(['message' => 'Lumen API is running'], 200);
});
