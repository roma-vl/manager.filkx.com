<?php

declare(strict_types=1);

namespace App\Tenant\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class AccountFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        // Для сутностей з безпосереднім зв'язком ManyToOne
        if ($targetEntity->hasAssociation('account')
            && $targetEntity->isSingleValuedAssociation('account')) {
            try {
                return sprintf('%s.account_id = %s', $targetTableAlias, $this->getParameter('account_id'));
            } catch (\InvalidArgumentException $e) {
                return '';
            }
        }

        // Для сутностей з полем account_id без явного зв'язку
        if ($targetEntity->hasField('account_id')) {
            try {
                return sprintf('%s.account_id = %s', $targetTableAlias, $this->getParameter('account_id'));
            } catch (\InvalidArgumentException $e) {
                return '';
            }
        }

        return '';
    }
}
