<?php

namespace GaylordP\UserBundle\Repository\Filter;

use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter;

class DeletedAtFilter extends SQLFilter
{
    public function addFilterConstraint(
        ClassMetadata $targetEntity,
        $targetTableAlias
    ): string {
        if ($targetEntity->hasField('deletedAt')) {
            return '' . $targetTableAlias . '.' . $targetEntity->getColumnName('deletedAt') . ' IS NULL';
        }

        return '';
    }
}
