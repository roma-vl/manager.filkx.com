<?php

declare(strict_types=1);

namespace App\OpenApi\Schema;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ViolationList',
    type: 'array',
    items: new OA\Items(
        properties: [
            new OA\Property(property: 'propertyPath', type: 'string'),
            new OA\Property(property: 'message', type: 'string'),
        ],
        type: 'object'
    )
)]
final class ViolationListSchema
{
}
