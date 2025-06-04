<?php

namespace App\Http\Controllers\Subscription;

use App\Models\Plan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Laravel\Cashier\Subscription;

class SubscriptionController extends Controller
{
    public function index(Request $request){
        // dd($request->user()->createSetupIntent());
        return view('subscription.checkout',[
            'intent' => $request->user()->createSetupIntent(),
        ]);
    }

    public function store(Request $request)
{
    DB::beginTransaction();

    try {
        $request->validate([
            'token' => 'required',
            'plan' => 'required|string'
        ]);

        $user = auth()->user();

        $plan = Plan::where('slug', $request->plan)->first();

        // Get current subscription record
        $subPlan = SubscriptionPlan::where('user_id', $user->id)->latest()->first();
        if($subPlan){
            $currentPlan=Plan::find($subPlan->plan_id);
            $currentSubscription = $user->subscription($currentPlan->slug);
            if ($currentSubscription && ($currentSubscription->active() || $currentSubscription->onGracePeriod())) {
                $newSubscription = $user->newSubscription($request->plan, $plan->stripe_id)
                ->create($request->token);
            }
        }




        // If subscription expired or doesn't exist
        $newSubscription = $user->newSubscription($request->plan, $plan->stripe_id)
            ->create($request->token);

        // If successfully subscribed
        if ($newSubscription->active()) {
            SubscriptionPlan::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'subscription_id' => $newSubscription->id,
                    'plan_id' => $plan->id,
                ]
            );

            DB::commit();
            return redirect()->route('dashboard')->with('success', 'Subscription created successfully!');
        }

        // If payment failed
        if ($newSubscription->incomplete()) {
            DB::rollBack();
            return back()->with('error', 'Payment failed.');
        }

        DB::commit();
        return back()->with('error', 'Unknown subscription state.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Error creating subscription: ' . $e->getMessage());
    }
}



    public function cancel(Request $request){
        $subscription=$request->user()->subscription($request->plan);
        $subscription->cancel();
        return to_route('dashboard')->with('success', 'Subscription cancelled successfully!');
    }
}
