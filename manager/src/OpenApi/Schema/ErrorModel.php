<?php

declare(strict_types=1);

namespace App\OpenApi\Schema;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ErrorModel',
    required: ['error'],
    properties: [
        new OA\Property(property: 'error', properties: [
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'code', type: 'integer'),
        ], type: 'object')
    ],
    type: 'object'
)]
final class ErrorModel
{
    // Просто декларація схеми
}
