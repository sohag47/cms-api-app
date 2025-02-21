<?php

namespace App\Enums;

enum StatusEnums: string
{
    //? Successful responses
    case ACTIVE  = 'active';
    case DRAFT  = 'draft';
    case INACTIVE  = 'inactive';
    case DISABLED  = 'disabled';
    // case SUCCESS = 'Successfully Fetched';
    // case CREATED = 'Item Created Successfully';
    // case ACCEPTED = 'Item Accepted Successfully';

    // case BAD_REQUEST = 'Invalid request parameters';
    // case UNAUTHORIZED = 'Unauthorized Error!';
    // case FORBIDDEN = 'Forbidden';
    // case NOT_FOUND = 'No Item Found!';
    // case NOT_ACCEPTABLE = 'Not Acceptable';
    // case VALIDATION_FAILED = 'Validation Failed!';

    // case SERVER_ERROR = 'Internal Server Error!';

    // // for custom message
    // case UPDATED = 'Item Updated Successfully';
    // case DELETED = 'Item Deleted Successfully';
    // case CONFIRMED = 'Item Confirmed Successfully';
    // case REJECTED  = 'Item Rejected Successfully';
    // case APPLIED   = 'Item Applied Successfully';
    // case SELECTED  = 'Item Selected Successfully';

    // public function message(): string
    // {
    //     return match($this){
    //         self::SUCCESS           => 'Successfully Fetched',
    //         self::CREATED           => 'Item Created Successfully',
    //         self::ACCEPTED          => 'Item Accepted Successfully',

    //         self::BAD_REQUEST       => 'Invalid request parameters',
    //         self::UNAUTHORIZED      => 'Unauthorized Error!',
    //         self::FORBIDDEN         => 'Forbidden',
    //         self::NOT_FOUND         => 'No Item Found!',
    //         self::NOT_ACCEPTABLE    => 'Not Acceptable',
    //         self::VALIDATION_FAILED => 'Validation Failed!',

    //         self::SERVER_ERROR      => 'Internal Server Error!',
            
    //         // for custom message
    //         self::UPDATED           => 'Item Updated Successfully',
    //         self::DELETED           => 'Item Deleted Successfully',
    //         self::SELECTED          => 'Selected',
    //         self::CONFIRMED         => 'Confirmed',
    //         self::REJECTED          => 'Rejected',
    //         self::APPLIED           => 'Applied'
    //     };
    // }


    public static function options()
    {
        $cases = self::cases();
        $options = array_map(
            fn ($status) => [
                'label' => self::text($status->value), 
                'value' => $status->value
            ],
            $cases
        );
        return $options;
    }

    public static function text($value)
    {
        return optional(self::tryFrom($value))->name;
    }

    public static function value($value)
    {
        return optional(self::tryFrom($value))->value;
    }

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