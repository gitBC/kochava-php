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


$app->get('/test', function(){

    return "<html><head></head><body class=\"hello\">test it</body></html>";
});
$app->post('/test', function(){

    return "<html><head></head><body class=\"hello\">test it</body></html>";
});

$app->get('/testJson', function(){

    return "{\"delivery_attempts\":1,\"response_code\":200,\"response_time\":112354,\"response_body\":\"\u003chtml\u003e\u003chead\u003e\u003c/head\u003e\u003cbody class=\\\"hello\\\"\u003etest it\u003c/body\u003e\u003c/html\u003e\",\"original_redis_key\":\"1504543368712317\"}";
});
$app->post('/testJson', function(){

    return "{\"delivery_attempts\":1,\"response_code\":200,\"response_time\":112354,\"response_body\":\"\u003chtml\u003e\u003chead\u003e\u003c/head\u003e\u003cbody class=\\\"hello\\\"\u003etest it\u003c/body\u003e\u003c/html\u003e\",\"original_redis_key\":\"1504543368712317\"}";
});