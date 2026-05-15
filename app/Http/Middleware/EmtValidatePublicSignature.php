<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\RouteCollectionInterface;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Routing\Middleware\ValidateSignature as BaseMiddleware;

use App\Classes\emtURL;

class EmtValidatePublicSignature extends BaseMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return \Illuminate\Http\Response
     *
     */
    public function handle($request, Closure $next, ...$args)
    {

        if (!emtURL::hasValidPublicSignature($request, true)) {
            return redirect('/');
            //throw new InvalidSignatureException;
        }

        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);

        return $response;
    }
}
