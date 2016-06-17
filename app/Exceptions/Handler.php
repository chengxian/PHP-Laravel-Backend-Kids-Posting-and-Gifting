<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        Log::error($e);

        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        // if (is_api_request()) {
        //     $code = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : $e->getCode();

        //     if ($e instanceof TokenExpireException) { 
        //         $message = 'token_expired';
        //     } else if ($e instanceof TokenInvalidException) {
        //         $message = 'token_invalid';
        //     } else if ($e instanceof JWTException) {
        //         $message = $e->getMessage() ?: 'could_not_create_token';
        //     } else if ($e instanceof NotFoundHttpException) {
        //         $message = $e->getMessage() ?: 'not_found';
        //     } else if ($e instanceof  Exception) {
        //         $message = $e->getMessage() ?: 'Something broken :(';
        //     }

        //     return response->json([
        //         'code' => $code ?: 400,
        //         'errors' => $message,
        //     ], $code ?: 400);
        // }
        
        return parent::render($request, $e);
    }
}
