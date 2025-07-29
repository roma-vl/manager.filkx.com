<?php

namespace App\Infrastructure\Pagination;

use Knp\Component\Pager\Pagination\PaginationInterface;

class PaginationViewFactory
{
    public function create(PaginationInterface $pagination, int $perPage): array
    {
        return [
            'currentPage' => $pagination->getCurrentPageNumber(),
            'lastPage' => max(1, (int) ceil($pagination->getTotalItemCount() / $perPage)),
            'total' => $pagination->getTotalItemCount(),
        ];
    }
}

