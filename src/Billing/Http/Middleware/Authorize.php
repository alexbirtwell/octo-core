<?php

namespace Octo\Billing\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Octo\Billing\BillingPortal;

class Authorize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $authorization = BillingPortal::isAuthorizedToPerform($request);

        if ($authorization instanceof RedirectResponse) {
            return $authorization;
        } elseif ($authorization) {
            return $next($request);
        }

        abort(403);
    }
}