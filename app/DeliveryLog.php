<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\User
 *
 * @mixin \Eloquent
 */
class DeliveryLog extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'original_redis_key',
        'delivery_method',
        'delivery_location',
        'delivery_attempts',
        'delivery_time_seconds',
        'response_body',
        'response_code',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'id'
    ];
}
