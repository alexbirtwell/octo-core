<?php

namespace Octo\Billing\Actions;

use Illuminate\Http\Request;
use Octo\Billing\Billing;
use Octo\Billing\Contracts\HandleSubscriptions as HandleSubscriptionsContract;
use Octo\Billing\Plan;

class HandleSubscriptions implements HandleSubscriptionsContract
{
    /**
     * Mutate the checkout object before redirecting the user to subscribe to a certain plan.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $billable
     * @param  \Octo\Billing\Plan  $plan
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function checkoutOnSubscription($subscription, $billable, Plan $plan, Request $request)
    {
        return $subscription->checkout([
            'success_url' => route('billing.subscription.index', ['success' => "You have successfully subscribed to {$plan->getName()}!"]),
            'cancel_url' => route('billing.subscription.index', ['error' => "The subscription to {$plan->getName()} was cancelled!"]),
        ]);
    }

    /**
     * Subscribe the user to a given plan.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $billable
     * @param  \Octo\Billing\Plan  $plan
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function subscribeToPlan($billable, Plan $plan, Request $request)
    {
        return $billable
            ->newSubscription($request->subscription, $plan->getId())
            ->create($billable->defaultPaymentMethod()->id);
    }

    /**
     * Swap the current subscription plan.
     *
     * @param  \Octo\Billing\Models\Stripe\Subscription  $subscription
     * @param  \Illuminate\Database\Eloquent\Model  $billable
     * @param  \Octo\Billing\Plan  $plan
     * @param  \Illuminate\Http\Request  $request
     * @return \Octo\Billing\Models\Stripe\Subscription
     */
    public function swapToPlan($subscription, $billable, Plan $plan, Request $request)
    {
        if (Billing::proratesOnSwap()) {
            return $subscription->swap($plan->getId());
        }

        return $subscription->noProrate()->swap($plan->getId());
    }

    /**
     * Define the logic to be called when the user requests resuming a subscription.
     *
     * @param  \Octo\Billing\Models\Stripe\Subscription  $subscription
     * @param  \Illuminate\Database\Eloquent\Model  $billable
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function resumeSubscription($subscription, $billable, Request $request)
    {
        $subscription->resume();
    }

    /**
     * Define the subscriptioncancellation action.
     *
     * @param  \Octo\Billing\Models\Stripe\Subscription  $subscription
     * @param  \Illuminate\Database\Eloquent\Model  $billable
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function cancelSubscription($subscription, $billable, Request $request)
    {
        $subscription->cancel();
    }
}
