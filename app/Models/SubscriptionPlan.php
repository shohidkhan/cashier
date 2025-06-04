<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stripe\Subscription;

class SubscriptionPlan extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

}
