<?php

namespace AcMarche\Sepulture\Doctrine;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class TablePrefix
{
    public function __construct(
        protected string $prefix,
        protected string $namespace
    ) {
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $classMetadata = $eventArgs->getClassMetadata();

        if (! preg_match('#'.$this->namespace.'#', $classMetadata->namespace)) {
            return;
        }

        if (! $classMetadata->isInheritanceTypeSingleTable() || $classMetadata->getName(
            ) === $classMetadata->rootEntityName) {
            $classMetadata->setPrimaryTable([
                'name' => $this->prefix.$classMetadata->getTableName(),
            ]);
        }

        foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
            if (ClassMetadataInfo::MANY_TO_MANY == $mapping['type'] && $mapping['isOwningSide']) {
                $mappedTableName = $mapping['joinTable']['name'];
                $classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $this->prefix.$mappedTableName;
            }
        }
    }
}
