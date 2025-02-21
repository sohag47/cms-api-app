<?php

namespace App\Traits;


use App\Enums\ApiResponseEnum;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    
    /**
     * @param $message
     * @param $successCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonResponse($response): JsonResponse
    {
        return response()->json($response, $response['code']);
    }

    /**
     * @param $message
     * @param $successCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithSuccess($data = null, $message = "", $code = Response::HTTP_OK): JsonResponse
    {
        $response_data = [
            'success'   => true,
            'code'      => !empty($code) ? $code : Response::HTTP_OK,
            'message'   => !empty($message) ? $message : ApiResponseEnum::ITEM_FOUND,
            'data'     =>  $data,
        ];
        return $this->jsonResponse($response_data);
    }

    protected function respondWithItem($data = null): JsonResponse
    {
        return $this->respondWithSuccess($data, ApiResponseEnum::ITEM_FOUND);
    }
    protected function respondWithCreated($data = null, $message = ''): JsonResponse
    {
        return $this->respondWithSuccess($data, !empty($message) ? $message : ApiResponseEnum::CREATED, Response::HTTP_CREATED);
    }
    protected function respondWithUpdated($data = null): JsonResponse
    {
        return $this->respondWithSuccess($data, ApiResponseEnum::UPDATED);
    }
    protected function respondWithDeleted(): JsonResponse
    {
        return $this->respondWithSuccess(null, ApiResponseEnum::DELETED);
    }

    protected function respondWithError($code = Response::HTTP_BAD_REQUEST, $message = "", $error = null): JsonResponse
    {
        $response_data = [
            'success' => false,
            'code'    => $code,
            'message' => $message,
            'error'   => $error,
            'data'    => null  
        ];
        return $this->jsonResponse($response_data);
    }

    protected function respondWithNotFound($errors = null, $messages = ""): JsonResponse
    {
        $message =  empty($messages) ? ApiResponseEnum::NOT_FOUND : $messages;
        $error = empty($errors) ? ApiResponseEnum::NOT_FOUND->errorMessage() : $errors;
        return $this->respondWithError(Response::HTTP_NOT_FOUND, $message, $error);
    }

    protected function respondValidationError($errors = null, $messages = ""): JsonResponse
    {
        $message =  empty($messages) ? ApiResponseEnum::VALIDATION_ERROR : $messages;
        $error = empty($errors) ? ApiResponseEnum::VALIDATION_ERROR->errorMessage() : $errors;
        return $this->respondWithError(Response::HTTP_UNPROCESSABLE_ENTITY, $message, $error);
    }

    protected function respondServerError($errors = null, $messages = ""): JsonResponse
    {
        $message =  empty($messages) ? ApiResponseEnum::SERVER_ERROR : $messages;
        $error = empty($errors) ? ApiResponseEnum::SERVER_ERROR->errorMessage() : $errors;
        return $this->respondWithError(Response::HTTP_INTERNAL_SERVER_ERROR, $message, $error);
    }

    protected function respondUnauthorizedError($messages = ""): JsonResponse
    {
        $message =  empty($messages) ? ApiResponseEnum::UNAUTHORIZED : $messages;
        $error = empty($errors) ? ApiResponseEnum::UNAUTHORIZED->errorMessage() : $errors;
        return $this->respondWithError(Response::HTTP_UNAUTHORIZED, $message, $error);
    }

}