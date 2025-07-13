<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueUserEmail extends Constraint
{
    public string $message = 'Користувач з email "{{ value }}" вже існує.';

    public function validatedBy(): string
    {
        return static::class . 'Validator';
    }

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
