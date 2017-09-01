<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use App\Http\Controllers\QueueController;

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->get('/ingest', 'QueueController@store');
$app->post('/ingest', 'QueueController@store');


$app->post('/digest', "DeliveryLogController@store");