<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;

class PlanController extends Controller
{

    /**
     * Display the subscription plans.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        Stripe::setApiKey(config('services.stripe.stripe_secret'));

        // Fetch all plans from DB
        $plans = Plan::all();

        // Enrich each plan with Stripe product & price details
        $planDetails = $plans->map(function ($plan) {
                $product = Product::retrieve($plan->product_id);
                // return $product;
                $price = Price::retrieve($plan->stripe_id);
                return [
                    'slug' => $plan->slug,
                    'product_name' => $product->name,
                    'metadata'=> $product->metadata,
                    'marketing_features' => $product->marketing_features,
                    'product_description' => $product->description,
                    'price_id' => $price->id,
                    'amount' => $price->unit_amount / 100,
                    'currency' => strtoupper($price->currency),
                    'interval' => $price->recurring->interval,
                ];
        });
            // return $planDetails;
        return view('subscription.plans',compact('planDetails'));
    }
}
