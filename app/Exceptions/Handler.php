<?php

namespace App\Exceptions;

use App\Enums\ApiResponseEnum;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponse;
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
        // Handle NotFoundHttpException (404)
        $this->renderable(function (NotFoundHttpException $e, $request) {
            return $this->respondWithNotFound();
        });

        // Handle MethodNotAllowedHttpException (405)
        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            return $this->respondWithError(
                Response::HTTP_METHOD_NOT_ALLOWED, 
                ApiResponseEnum::METHOD_NOT_ALLOWED, 
                ApiResponseEnum::METHOD_NOT_ALLOWED->errorMessage()
            );
        });

        // Handle UnauthorizedHttpException (401)
        $this->renderable(function (UnauthorizedHttpException $e, $request) {
            return $this->respondWithError(
                Response::HTTP_UNAUTHORIZED, 
                ApiResponseEnum::UNAUTHORIZED, 
                ApiResponseEnum::UNAUTHORIZED->errorMessage()
            );
        });

        // Handle UnauthorizedHttpException (401)
        $this->renderable(function (RouteNotFoundException $e, $request) {
            return $this->respondWithNotFound($e->getTraceAsString(), $e->getMessage());
        });


        // Handle various HTTP error codes
        $this->renderable(function (HttpException $e, $request) {
            // Centralize error handling for 4xx and 5xx errors
            $statusCode = $e->getStatusCode();
            
            // Handle common errors in a consistent way
            switch ($statusCode) {
                case Response::HTTP_INTERNAL_SERVER_ERROR:
                    return $this->respondServerError(
                        ApiResponseEnum::SERVER_ERROR, 
                        ApiResponseEnum::SERVER_ERROR->errorMessage()
                    );
                case Response::HTTP_FORBIDDEN:
                    return $this->respondWithError(
                        Response::HTTP_FORBIDDEN,
                        ApiResponseEnum::FORBIDDEN, 
                        ApiResponseEnum::FORBIDDEN->errorMessage()
                    );
                case Response::HTTP_UNPROCESSABLE_ENTITY:
                    return $this->respondValidationError(
                        ApiResponseEnum::VALIDATION_ERROR, 
                        ApiResponseEnum::VALIDATION_ERROR->errorMessage()
                    );
                case Response::HTTP_TOO_MANY_REQUESTS:
                    return $this->respondWithError(
                        Response::HTTP_TOO_MANY_REQUESTS,
                        'Too Many Requests',
                        'You have sent too many requests. Please try again later.'
                    );
                case Response::HTTP_SERVICE_UNAVAILABLE:
                    return $this->respondWithError(
                        Response::HTTP_SERVICE_UNAVAILABLE,
                        'Service Unavailable',
                        'The server is currently unavailable. Please try again later.'
                    );
                default:
                    // Fallback to parent handler for other cases
                    return parent::render($request, $e);
            }
        });
    }
}
