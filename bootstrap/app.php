<?php

// STEP 13 — wire the global exception handler.
//
// Laravel 11 no longer has an `app/Exceptions/Handler.php`. Everything
// exception-related is configured here in `bootstrap/app.php` through the
// fluent `withExceptions` callback.
//
// What we're doing below: for any request that starts with `/api/`, turn
// uncaught exceptions into a consistent JSON envelope instead of the
// default HTML stack trace. Web (Blade) requests keep Laravel's native
// error pages untouched.

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Single render callback. `render()` with a closure is called for
        // every unhandled exception; returning a Response short-circuits
        // Laravel's default rendering. Returning null would let it through.
        $exceptions->render(function (\Throwable $e, Request $request) {
            if (! $request->is('api/*')) {
                return null; // let Blade / default handler deal with web.
            }

            // Validation failures carry their own `status` (422) and an `errors`
            // bag. Emit those separately so clients get the field-by-field
            // breakdown they need to show inline form errors.
            if ($e instanceof ValidationException) {
                return response()->json([
                    'error'   => 'ValidationException',
                    'status'  => 422,
                    'message' => $e->getMessage(),
                    'errors'  => $e->errors(),
                ], 422);
            }

            // Any Symfony HttpException (NotFoundHttpException,
            // ThrottleRequestsException, etc.) already knows its status code.
            $status = $e instanceof HttpExceptionInterface
                ? $e->getStatusCode()
                : 500;

            return response()->json([
                'error'   => class_basename($e),
                'status'  => $status,
                // `APP_DEBUG=true` gets the real message; in production users
                // see a vague string so we don't leak internals by accident.
                'message' => config('app.debug') ? $e->getMessage() : 'Something went wrong.',
            ], $status);
        });
    })->create();
