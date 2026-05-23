<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\EmtValidatePublicSignature;
use App\Http\Middleware\EmtValidateSignature;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'subscribed' => Authenticate::class,
            'emt-public-signed' => EmtValidatePublicSignature::class,
            'emt-signed' => EmtValidateSignature::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
