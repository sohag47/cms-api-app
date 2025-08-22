<?php

namespace App\Enums\Blog;

enum PostStatusEnum: string
{
    case DRAFT = 'draft';
    case PENDING_REVIEW = 'pending_review';
    case SCHEDULED = 'scheduled';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';

    public static function options()
    {
        $cases = self::cases();
        $options = array_map(
            fn ($status) => [
                'label' => self::text($status->value),
                'value' => $status->value,
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
}
