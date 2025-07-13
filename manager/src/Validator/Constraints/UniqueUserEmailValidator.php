<?php

declare(strict_types=1);

// src/Validator/Constraints/UniqueUserEmailValidator.php

namespace App\Validator\Constraints;

use App\ReadModel\User\UserFetcher;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueUserEmailValidator extends ConstraintValidator
{
    public function __construct(
        private readonly UserFetcher $users,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueUserEmail) {
            throw new UnexpectedTypeException($constraint, UniqueUserEmail::class);
        }

        if (!\is_string($value) || empty($value)) {
            return;
        }

        if ($this->users->existsByEmail($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
