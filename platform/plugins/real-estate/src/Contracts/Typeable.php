<?php

namespace Botble\RealEstate\Contracts;

trait Typeable
{
    public function stringToArray(?string $string): array
    {
        if ($string === null) {
            return [];
        }

        return explode(',', $string);
    }

    public function yesNoToBoolean(?string $string): bool
    {
        return strtolower($string) === 'yes';
    }
}
