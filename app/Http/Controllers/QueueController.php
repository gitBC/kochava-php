<?php


namespace App\Http\Controllers;


use App\DeliveryLog;
use App\Jobs\QueueJob;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\Rule;
use function PHPSTORM_META\type;

class QueueController extends Controller {


    /**
     * QueueController constructor.
     */
    function __construct () {

    }

    /**
     * @param Request $request
     */
    public function store(Request $request){


        /*
         * Validate the request
         */
        $this->validate($request, [
            'endpoint' => 'required',
            'endpoint.method' => ['required', Rule::in('GET', 'POST')],
            'endpoint.url' => 'required',
            'data' => 'required|array',
            'data.*.mascot' => 'required',
            'data.*.location' => 'required|url',

        ]);

        /*
         * Store relevant parts of the request
         */
        $r = $request->only('data', 'endpoint');

        /*
         * Create response array to return added items
         */
        $added = [];

        /*
         * loop over our data object
         */
        foreach ( $r['data'] as $item){

            //Store time to build key
            $time = Carbon::now();

            /*
             * Make Item array to manipulate and add to result array
             */
            $newItem = [
                'method' => $r['endpoint']['method'],
                'location' => $r['endpoint']['url']
            ];

            /*
             * Loop over the data keys to build callback url
             *
             * We will add any additional subkeys or subIDs to the end of the request URL for customer data tracking
             */
            foreach ($item as $key => $value){

                if (strpos($newItem['location'], '{'.$key.'}') > -1){
                    $newItem['location'] = str_replace('{'.$key.'}', urlencode($value), $newItem['location']);
                } else {
                    $newItem['location'] .= '&'. urlencode($key) .'='. urlencode($value);
                }

            }


            //TODO: Bar isnt the only item which could have been in the request. We need to remove any empty replace value
            //Remove {bar} if it doesnt exist in array keys
            if (strpos($newItem['location'], '{bar}') > -1){
                $newItem['location'] = preg_replace("/\{bar\}/",'', $newItem['location']);
            }


            //Push an Item onto Redis, delivery_logs table, and built up response
            Redis::set($time->timestamp . $time->micro, json_encode($newItem));
            array_push($added,$newItem );

            DeliveryLog::create([
                'original_redis_key'=> $time->timestamp . $time->micro,
                'delivery_method'=> $newItem['method'],
                'delivery_location'=> $newItem['location']
            ]);

        }


        /*
         * Send back the results we added to redis
         */
        return json_encode($added);
    }

}