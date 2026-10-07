<?php

namespace App\Support;

final class GuestBrand
{
    public const NAME = 'daycare.ai.id';

    public static function name(): string
    {
        return self::NAME;
    }
}
