<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Subscription\PlanController;
use App\Http\Controllers\Subscription\SubscriptionController;
use App\Models\Plan;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    $subPlan = SubscriptionPlan::where('user_id', $user->id)->first();



    if (!$subPlan) {
        return redirect()->route('plans.index')->with('error', 'No active subscription found.');
    }

    $plan =Plan::findOrFail($subPlan->plan_id);

    return view('dashboard', ['plan' => $plan]);
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('/subscription',[SubscriptionController::class,'index'])->name('subscription.index');
    Route::post('/subscription',[SubscriptionController::class,'store'])->name('subscription.store');
    Route::post('/subscription/cancel',[SubscriptionController::class,'cancel'])->name('subscription.cancel');
});
Route::get('/plans', [PlanController::class,'index'])->name('plans.index');



require __DIR__.'/auth.php';
