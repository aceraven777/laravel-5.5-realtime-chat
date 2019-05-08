<?php

namespace App;

use App\Events\ChatSent;
use Carbon\Carbon as Carbon;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'from_user_id', 'to_user_id', 'text',
    ];

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

        static::creating(function($model) {
            $model->setCreatedAt($model->freshTimestamp());
        });

        static::created(function($model) {
            broadcast(new ChatSent($model));
        });
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->toATOMString();
    }

    public function fromUser()
    {
        return $this->belongsTo('App\User', 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo('App\User', 'to_user_id');
    }
}
