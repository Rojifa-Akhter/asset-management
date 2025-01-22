<?php


use App\Http\Middleware\LocationEmployee;
use App\Http\Middleware\Organization;
use App\Http\Middleware\SuperAdmin;
use App\Http\Middleware\SupportAgent;
use App\Http\Middleware\Technician;
use App\Http\Middleware\ThirdParty;
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
            'super_admin' => SuperAdmin::class,
            'organization' => Organization::class,
            'location_employee' => LocationEmployee::class,
            'support_agent' => SupportAgent::class,
            'third_party' => ThirdParty::class,
            'technician' => Technician::class,
            'user' => User::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
