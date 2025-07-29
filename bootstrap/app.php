<?php

use Illuminate\Http\Request;
use App\Console\Commands\CreateAdminAccount;
use App\Console\Commands\CreateAdminAccountCommand;
use App\Console\Commands\CreateCustomModel;
use App\Console\Commands\ExportPostmanMyVersion;
use App\Console\Commands\ExportPostmanTesto;
use App\Console\Commands\GenerateResources;
use App\Helpers\ApiResponse;
use App\Http\Middleware\EnsureRiderProfileComplete;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Exceptions\MissingAbilityException;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,

        ]);
        $middleware->alias([
            'rider.profile.completed' => EnsureRiderProfileComplete::class
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return ApiResponse::sendResponseError(statusCode: 401, message: 'Not Authenticated',);
        });

        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            // Traverse the exception chain to find MissingAbilityException
            $previousException = $e->getPrevious();
            while ($previousException) {
                if ($previousException instanceof MissingAbilityException) {
                    return ApiResponse::sendResponseError(statusCode: 403, message: 'You do not have the required authorization to perform this action.');
                }
                $previousException = $previousException->getPrevious();
            }
            // If MissingAbilityException is not found, proceed with default handling
            return ApiResponse::sendResponseError(statusCode: 403, message: 'you are not authorized');
        });
    })
    ->withCommands([
        CreateCustomModel::class,
        GenerateResources::class,
        CreateAdminAccountCommand::class,
        ExportPostmanTesto::class,
        ExportPostmanMyVersion::class,
    ])->create();
