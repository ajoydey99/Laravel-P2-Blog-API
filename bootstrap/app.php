<?php

use App\Http\Middleware\RoleCheckMiddleware;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleCheckMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // for database connection
        $exceptions->render(function (QueryException $e, Request $request) {
            return response()->json([
                'status'  => false,
                'message' => 'Database connection failed',
            ], 500);
        });

        // for resource not found
        $exceptions->render(function (NotFoundHttpException | ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                $messages = [
                    'api/*/posts/*' => 'Post not found',
                    'api/*/tags/*'  => 'Tag not found',
                    'api/*/users/*' => 'User not found',
                ];

                foreach ($messages as $pattern => $message) {
                    if ($request->is($pattern)) {
                        return response()->json([
                            'status'  => false,
                            'message' => $message,
                        ], 404);
                    }
                }

                return response()->json([
                    'status'  => false,
                    'message' => 'Resource not found',
                ], 404);
            }
        });

        // for authorization
        $exceptions->render(function (AuthorizationException | AccessDeniedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'You are not authorized to perform this action.',
                    'exception' => get_class($e),
                ], 403);
            }
        });
    })->create();