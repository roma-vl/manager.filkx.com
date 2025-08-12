<?php

declare(strict_types=1);

namespace App\DTO\Work\Member;

use Symfony\Component\Serializer\Annotation\Groups;

class MemberDto
{
    #[Groups(['task:list'])]
    public string $id;

    #[Groups(['task:list'])]
    public string $name;

    #[Groups(['task:list'])]
    public string $group;
}
