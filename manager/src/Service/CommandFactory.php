<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class CommandFactory
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {}

    /**
     * @param object $command - інстанс класу команди (DTO)
     * @return array{command: object, errors: array<string, string>}
     */
    public function createFromRequest(Request $request, object $command): array
    {
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            $data = [];
        }

        foreach ($data as $key => $value) {
            if (property_exists($command, $key)) {
                $command->$key = $value;
            }
        }

        $violations = $this->validator->validate($command);
        $errors = [];

        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        return $errors;
    }
}
