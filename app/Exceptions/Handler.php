<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];


    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        // Check if the request expects JSON (i.e., it's an API request)
        if ($request->expectsJson()) {

            // Handle 500 errors
            if ($exception instanceof HttpException && $exception->getStatusCode() == 500) {
                return response()->json([
                    'error' => 'Server Error',
                    'message' => 'An unexpected error occurred. Please try again later.',
                    'details' => env('APP_DEBUG') === 'true' ? $exception->getMessage() : null
                ], 500);
            }

            // For other exceptions, return the default error response
            return response()->json([
                'error' => 'Server Error',
                'message' => 'An unexpected error occurred.',
            ], 500);
        }

        // For non-API requests, use the default Laravel error handling
        return parent::render($request, $exception);
    }
}
