<?php


namespace App\Http\Controllers;


use App\DeliveryLog;
use App\Jobs\QueueJob;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\Rule;
use Log;
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

        if (getenv("APP_DEBUG")) {
            Log::debug("************************* New Request to deliver *************************\n" . $request);
        }


        /*
         * Validate the request
         */
        $this->validate($request, [
            'endpoint' => 'required',
            'endpoint.method' => ['required', Rule::in('GET', 'POST')],
            'endpoint.url' => 'required',
            'data' => 'required|array',
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

            if (getenv("APP_DEBUG")) {
                Log::debug("************************* Prepping New Item for Queue *************************\n" . print_r($item, true));
            }

            //Store time to build key
            $time = number_format ( microtime(true),  $decimals = 6, $dec_point = ".", $thousands_sep = "" );

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
             * This works in two ways. We can either replace a key in the endpoint url or we can append
             * additional key value pairs to the end of the string if they dont appear in the as keys for
             * replacement in the endpoint url
             *
             * We will add any additional subkeys or subIDs to the end of the request URL for additional
             * customer data tracking
             *
             * Additionally, you can define default values for either key pair in the .env file
             */
            foreach ($item as $key => $value){


                if ($value === "") {
                    $value = getenv("DEFAULT_KEY_" . strtoupper($key));
                }

                if (strpos($newItem['location'], '{'.$key.'}') > -1){
                    if (getenv("APP_DEBUG")) {
                        Log::debug("************************* Replacing An Item Value in Queue *************************\n"
                        . "Replacing {" . $key . "} with " .$value );
                    }

                    $newItem['location'] = str_replace('{'.$key.'}', urlencode($value), $newItem['location']);

                } else {

                    if (getenv("APP_DEBUG")) {

                        Log::debug("************************* Adding SubKey in Queue *************************\n"
                            . "Adding " . $key . "=" .$value );
                    }

                    $newItem['location'] .= '&'. urlencode($key) .'='. urlencode($value);

                }

            }


            //Remove any replacements if it doesnt exist in array keys
            if (preg_match("{.*?}", $newItem['location']) === 1){
                $newItem['location'] = preg_replace("/\{.*?\}/",'', $newItem['location']);
            }



            Log::debug("************************* Adding New Item to Queue *************************\n"
                . print_r($newItem, true));

            $newItem['original_request_time'] = $time;

            //Push an Item onto Redis, delivery_logs table, and built up response
            Redis::rpush("queue:requests", json_encode($newItem));
            array_push($added,$newItem );



            $logItem = DeliveryLog::create([
                'original_redis_key'=> $time,
                'delivery_method'=> $newItem['method'],
                'delivery_location'=> $newItem['location']
            ]);

            Log::debug("************************* Adding Delivery Log Item*************************\n"
                . print_r($logItem->getAttributes(), true) );

        }


        /*
         * Send back the results we added to redis
         */
        return response()->json($added);
    }

}