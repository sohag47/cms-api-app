<?php

namespace App\Enums;

enum ApiResponseEnum: string
{
    //? Successful responses
    // case LOGIN_SUCCESS  = 'User Login Successfully';
    // case SUCCESS = 'Successfully Fetched';

    // case ACCEPTED = 'Item Accepted Successfully';

    // case BAD_REQUEST = 'Invalid request parameters';
    case UNAUTHORIZED = 'Unauthorized Access!';
    case FORBIDDEN = 'Forbidden';
    case ITEM_FOUND = 'Resource Found Successfully';
    case NOT_FOUND = 'Resource Not Found!';
    // case NOT_ACCEPTABLE = 'Not Acceptable';
    case VALIDATION_ERROR = 'Validation Error!';

    case SERVER_ERROR = 'Internal Server Error!';
    case METHOD_NOT_ALLOWED = 'Http Method Not Allowed';
    // for custom message
    case CREATED = 'Item Created Successfully';
    case UPDATED = 'Item Updated Successfully';
    case DELETED = 'Item Deleted Successfully';

    // case CONFIRMED = 'Item Confirmed Successfully';
    // case REJECTED  = 'Item Rejected Successfully';
    // case APPLIED   = 'Item Applied Successfully';
    // case SELECTED  = 'Item Selected Successfully';
    // case RESTORED = 'Item Restored Successfully';

    public function errorMessage(): string
    {
        return match($this){
            // self::SUCCESS           => 'Successfully Fetched',
            // self::ACCEPTED          => 'Item Accepted Successfully',

            // self::BAD_REQUEST       => 'Invalid request parameters',
            self::UNAUTHORIZED      => 'You are not authorized to access this resource. Please authenticate.',
            self::FORBIDDEN         => 'You do not have permission to access this resource.',
            self::NOT_FOUND         => 'The requested resource could not be found.',
            // self::NOT_ACCEPTABLE    => 'Not Acceptable',
            self::VALIDATION_ERROR => 'The given data was invalid.',

            self::SERVER_ERROR      => 'An unexpected error occurred on the server. Please try again later.',
            self::METHOD_NOT_ALLOWED => 'The HTTP method used is not allowed for this route.',
            // for custom message
            self::CREATED           => 'Item Created Successfully',
            self::UPDATED           => 'Item Updated Successfully',
            self::DELETED           => 'Item Deleted Successfully',
            // self::SELECTED          => 'Selected',
            // self::CONFIRMED         => 'Confirmed',
            // self::REJECTED          => 'Rejected',
            // self::APPLIED           => 'Applied'
        };
    }


    // public static function options()
    // {
    //     $cases = self::cases();
    //     $options = array_map(
    //         fn ($status) => ['text' => self::text($status->value), 'value' => $status->value],
    //         $cases
    //     );
    //     return $options;
    // }

    // public static function text($value)
    // {
    //     return optional(self::tryFrom($value))->status();
    // }

    // public static function value($value)
    // {
    //     return optional(self::tryFrom($value))->value;
    // }

    // public function applied()
    // {
    //     return self::SUCCESS->value;
    // }

    // case CREATE         = 'Item created successfully';
    // case UPDATE         = 'Item updated successfully';
    // case DELETE         = 'Item deleted successfully';
    // case NOT_FOUND       = 'Item Not Found!!!';
    // case SUCCESS        = "Successfully Fetched";
}