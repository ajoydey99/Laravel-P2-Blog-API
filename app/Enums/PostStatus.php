<?php
namespace App\Enums;

enum PostStatus: int {
    case Pending   = 0;
    case Published = 1;
    case Rejected  = 2;

    public function label(): string
    {
        return match ($this) {
            self::Pending   => 'Pending',
            self::Published => 'Published',
            self::Rejected  => 'Rejected',
        };
    }
}