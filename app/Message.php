<?php

namespace App;

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
