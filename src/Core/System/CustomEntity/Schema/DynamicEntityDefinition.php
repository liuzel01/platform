<?php declare(strict_types=1);

namespace Shuwei\Core\System\CustomEntity\Schema;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Flag;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shuwei\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shuwei\Core\Framework\Log\Package;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @internal The use of this class is reserved for the custom_entity feature
 */
#[Package('core')]
class DynamicEntityDefinition extends EntityDefinition
{
    protected string $name;

    /**
     * @var list<array{name: string, type: string, required?: bool, translatable?: bool, reference: string, inherited?: bool, onDelete: string, storeApiAware?: bool}>
     */
    protected array $fieldDefinitions;

    /**
     * @var list<Flag>
     */
    protected array $flags;

    protected ContainerInterface $container;

    /**
     * @param list<array{name: string, type: string, required?: bool, translatable?: bool, reference: string, inherited?: bool, onDelete: string, storeApiAware?: bool}> $fields
     * @param list<Flag> $flags
     */
    public static function create(
        string $name,
        array $fields,
        array $flags,
        ContainerInterface $container
    ): DynamicEntityDefinition {
        $self = new self();
        $self->name = $name;
        $self->fieldDefinitions = $fields;
        $self->container = $container;
        $self->flags = $flags;

        return $self;
    }

    public function getEntityName(): string
    {
        return $this->name;
    }

    /**
     * @return list<Flag>
     */
    public function getFlags(): array
    {
        return $this->flags;
    }

    protected function defineFields(): FieldCollection
    {
        $collection = DynamicFieldFactory::create($this->container, $this->getEntityName(), $this->fieldDefinitions);

        $collection->add(
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new Required(), new PrimaryKey()),
        );

        return $collection;
    }
}
