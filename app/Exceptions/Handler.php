<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // For API requests (dubsync routes), always return JSON
        if ($request->is('dubsync/*') || $request->expectsJson()) {
            \Log::error('API Exception', [
                'url' => $request->path(),
                'method' => $request->method(),
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ]);

            return response()->json([
                'success' => false,
                'error' => $exception->getMessage() ?: 'An error occurred',
                'exception' => config('app.debug') ? get_class($exception) : null
            ], 500);
        }

        return parent::render($request, $exception);
    }
}
