<?php

declare(strict_types=1);

namespace App\ReadModel\Props;

interface PropsProviderInterface
{
    /**
     * @param array<string, mixed> $params
     */
    public function getProps(array $params = []): array;
}
