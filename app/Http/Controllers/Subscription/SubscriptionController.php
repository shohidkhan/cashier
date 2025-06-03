<?php

namespace App\Http\Controllers\Subscription;

use App\Models\Plan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class SubscriptionController extends Controller
{
    public function index(Request $request){
        // dd($request->user()->createSetupIntent());
        return view('subscription.checkout',[
            'intent' => $request->user()->createSetupIntent(),
        ]);
    }

    public function store(Request $request){
        $request->validate([
            'token' => 'required',
        ]);

        $plan = Plan::where('slug',$request->plan)->first();

        $request->user()->newSubscription($request->plan, $plan->stripe_id)
            ->create($request->token);
            return to_route('dashboard')->with('success', 'Subscription created successfully!');
    }


    public function cancel(Request $request){
        $request->user()->subscription($request->plan)->cancel();
        return to_route('dashboard')->with('success', 'Subscription cancelled successfully!');
    }
}
