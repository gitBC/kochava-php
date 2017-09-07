<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\User
 *
 * @mixin \Eloquent
 * @property int $id
 * @property string $original_redis_key
 * @property string $delivery_method
 * @property string $delivery_location
 * @property int $delivery_attempts
 * @property int $delivery_time_microseconds
 * @property string $response_body
 * @property int $response_code
 * @property int $response_time_microseconds
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeliveryLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeliveryLog whereDeliveryAttempts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeliveryLog whereDeliveryLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeliveryLog whereDeliveryMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeliveryLog whereDeliveryTimeMicroseconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeliveryLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeliveryLog whereOriginalRedisKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeliveryLog whereResponseBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeliveryLog whereResponseCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeliveryLog whereResponseTimeMicroseconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeliveryLog whereUpdatedAt($value)
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
