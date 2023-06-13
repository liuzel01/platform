<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityProtection\EntityProtectionCollection;
use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
abstract class EntityExtension
{
    /**
     * Allows to add fields to an entity.
     *
     * To load fields by your own, add the \Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Runtime flag to the field.
     * Added fields should have the \Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Extension which tells the DAL that this data
     * is not include in the struct and collection classes
     */
    public function extendFields(FieldCollection $collection): void
    {
    }

    /**
     * Allows to add protections to an entity
     *
     * Add the protections you need to the given `$protections`
     */
    public function extendProtections(EntityProtectionCollection $protections): void
    {
    }

    /**
     * Defines which entity definition should be extended by this class
     */
    abstract public function getDefinitionClass(): string;
}
