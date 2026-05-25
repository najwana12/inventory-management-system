<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\StaffMiddleware;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Http\Middleware\DetectChangeAppLanguage;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([

            // admin & super admin bisa akses CRUD
            'employee.middleware' => StaffMiddleware::class,

            // hanya super admin bisa validasi
            'super.admin' => SuperAdminMiddleware::class,

            // localization
            'localization' => DetectChangeAppLanguage::class,

        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();