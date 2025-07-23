<?php

declare(strict_types=1);

namespace App\Service\Work\Processor\Driver;

use App\ReadModel\Work\Members\Member\MemberFetcher;

class MemberDriver implements Driver
{
    private const PATTERN = '/\@[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}/i';

    private $members;

    public function __construct(MemberFetcher $members)
    {
        $this->members = $members;
    }

    public function process(string $text): string
    {
        return preg_replace_callback(self::PATTERN, function (array $matches) {
            $id = ltrim($matches[0], '@');
            if (!$member = $this->members->find($id)) {
                return $matches[0];
            }

            return sprintf(
                '<a href="/work/members/%s" class="text-blue-600 hover:underline">@%s</a>',
                $member->getId()->getValue(),
                htmlspecialchars($member->getName()->getFull())
            );
        }, $text);
    }

}
