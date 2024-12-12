<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api/v1')
                ->group(base_path('routes/api_v1.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // Array of closures.
        $handlers = [
            ValidationException::class => function (ValidationException $exception) {
                $errors = [];
                foreach ($exception->errors() as $key => $value) {
                    foreach ($value as $message) {
                        $errors[] = [
                            'status' => 422,
                            'message' => $message,
                            'source' => $key,
                        ];
                    }
                }

                return response()->json(['errors' => $errors], 422);
            },
            ModelNotFoundException::class => function (ModelNotFoundException $exception) {
                return response()->json([
                    'errors' => [
                        [
                            'status' => 404,
                            'message' => 'The resource does NOT exist.',
                        ],
                    ],
                ], 404);
            },
            NotFoundHttpException::class => function (NotFoundHttpException $exception) {
                return response()->json([
                    'errors' => [
                        [
                            'status' => 404,
                            'message' => 'The resource does NOT exist.',
                        ],
                    ],
                ], 404);
            },
            AuthenticationException::class => function (AuthenticationException $exception) {
                return response()->json([
                    'errors' => [
                        [
                            'status' => 401,
                            'message' => 'Unauthenticated',
                        ],
                    ],
                ], 401);
            },
        ];

        $exceptions->render(function (Throwable $exception, Request $request) use ($handlers) {
            $className = get_class($exception);

            if (array_key_exists($className, $handlers)) {
                $handler = $handlers[$className];

                return $handler($exception);

                // We can provide accurate response status
                // But security experts advice not to.
                //                return response()->json([
                //                    'errors' => $handler($exception)
                //                ],
                //                    $className === ValidationException::class ? 422 :
                //                        ($className === ModelNotFoundException::class ? 404 :
                //                            ($className === AuthenticationException::class ? 401 : 500))
                //                );
            }

            return response()->json([
                'errors' => [
                    [
                        'type' => class_basename($exception),
                        'status' => 500,
                        'message' => $exception->getMessage(),
                        'source' => 'Line: '.$exception->getLine().': '.$exception->getFile(),
                    ],
                ],
            ], 500);
        });

    })->create();
