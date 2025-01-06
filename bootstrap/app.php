<?php

use App\Http\Middleware\locationEmployee;
use App\Http\Middleware\Organization;
use App\Http\Middleware\superAdmin;
use App\Http\Middleware\supportAgent;
use App\Http\Middleware\Technician;
use App\Http\Middleware\thirdParty;
use App\Http\Middleware\User;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'Super Admin' => superAdmin::class,
            'Organization' => Organization::class,
            'Location Employee' => locationEmployee::class,
            'Support Agent' => supportAgent::class,
            'Third Party' => thirdParty::class,
            'Technician' => Technician::class,
            'User' => User::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
