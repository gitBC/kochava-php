<?php


namespace App\Http\Controllers;


use App\Jobs\QueueJob;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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



        $this->validate($request, [
            'endpoint' => 'required',
            'endpoint.method' => ['required', Rule::in('GET', 'POST')],
            'endpoint.url' => 'required',
            'data' => 'required|array',
            'data.*.mascot' => 'required',
            'data.*.location' => 'required|url',

        ]);

        return dd($request->only(['endpoint', 'data']));

/*
        $this->validate($request->
        only('endpoint', 'data'), [
           'endpoint' =>
        ]);*/

        dd($request);
//        $this->dispatch(new QueueJob($data));
    }

}