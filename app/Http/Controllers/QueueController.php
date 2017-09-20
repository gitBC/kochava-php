<?php


namespace App\Http\Controllers;


use App\DeliveryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\Rule;
use Log;
use function PHPSTORM_META\type;

class QueueController extends Controller {

    /**
     * QueueController constructor.
     */
    function __construct()
    {

    }

    /**
     * @param Request $request
     */
    public function store(Request $request)
    {

        $this->DebugLog("New Request to deliver", $request);

        /*
         * Validate the request
         */
        $this->validate($request, [
            'endpoint'        => 'required',
            'endpoint.method' => ['required', Rule::in('GET', 'POST')],
            'endpoint.url'    => 'required',
            'data'            => 'required|array',
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
        foreach ($r['data'] as $item)
        {

            $this->DebugLog("Prepping New Item for Queue", print_r($item, true));

            //Store time to build key
            $time = number_format(microtime(true), $decimals = 6, $dec_point = ".", $thousands_sep = "");

            /*
             * Make Item array to manipulate and add to result array
             */
            $newItem = [
                'method'   => $r['endpoint']['method'],
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
            foreach ($item as $key => $value)
            {


                if ($value === "")
                {
                    $value = getenv("DEFAULT_KEY_" . strtoupper($key));
                }

                if (strpos($newItem['location'], '{' . $key . '}') > - 1)
                {

                    $this->DebugLog("Replacing An Item Value in Queue", "Replacing {" . $key . "} with " . $value);

                    $newItem['location'] = str_replace('{' . $key . '}', urlencode($value), $newItem['location']);

                } else
                {

                    $this->DebugLog("Adding SubKey in Queue", "Adding " . $key . "=" . $value);

                    $newItem['location'] .= '&' . urlencode($key) . '=' . urlencode($value);

                }

            }


            //Remove any replacements if it doesnt exist in array keys
            if (preg_match("{.*?}", $newItem['location']) === 1)
            {
                $newItem['location'] = preg_replace("/\{.*?\}/", '', $newItem['location']);
            }


            Log::debug("************************* Adding New Item to Queue *************************\n"
                . print_r($newItem, true));

            $newItem['original_request_time'] = $time;

            //Push an Item onto an array so we can create Redis entries, delivery_logs table, and build up response
            array_push($added, $newItem);

        }

        //Insert each item into the delivery log table, give me back an array I can use later
        //Sadly, if we want to retain Model events, we have to make a call to the DB for each insert.
        //although its technically possible with the query builder, this will retain 100% functionality
        //at the expense of performance. This will however, allow us to retain an event driven system.
        $logItems = collect($added)->each(function ($item) {
            return DeliveryLog::create([
                'original_redis_key' => $item['original_request_time'],
                'delivery_method'    => $item['method'],
                'delivery_location'  => $item['location']
            ]);
        })->map(function ($item) {
            return json_encode($item);
        });


        Log::debug("************************* Adding Delivery Log Item*************************\n"
            . print_r($logItems, true));


        //call the rpush method only once now and add all items simultaneously.
        call_user_func_array(
            [Redis::class, 'rpush'],
            array_merge(
                ["queue:requests"], $logItems->toArray()
            )
        );


        /*
         * Send back the results we added to redis
         */

        return response()->json($added);
    }

    /**
     * Adds a debug message to the logs with the specified title when the app is in debug mode
     * @param $message
     */
    protected function DebugLog($title, $message)
    {
        if (getenv("APP_DEBUG"))
        {

            Log::debug("************************* ". $title ." *************************\n" . $message);
        }
    }

}