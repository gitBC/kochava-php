<?php

namespace App\Http\Controllers;


use App\DeliveryLog;
use Illuminate\Http\Request;

class DeliveryLogController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * crude method which Stores statistics about a records delivery
     *
     * @param Request $request
     */
    public function store(Request $request)
    {

        $record = DeliveryLog::where('original_redis_key', $request->input('original_redis_key'))->firstOrFail();

        $record->delivery_attempts = $request->input('delivery_attempts');
        $record->response_body = utf8_decode($request->input('response_body'));
        $record->response_code = $request->input('response_code');
        $record->response_time_microseconds = $request->input('response_time_delta');
        $record->delivery_time_microseconds = $request->input('delivery_time_delta');
        \Log::debug("Time to deliver = " . $record->delivery_time_microseconds . " seconds");

        $record->save();
    }
}
