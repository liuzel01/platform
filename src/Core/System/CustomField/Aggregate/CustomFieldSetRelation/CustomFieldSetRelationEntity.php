<?php declare(strict_types=1);

namespace Shuwei\Core\System\CustomField\Aggregate\CustomFieldSetRelation;

use Shuwei\Core\Framework\DataAbstractionLayer\Entity;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\CustomField\Aggregate\CustomFieldSet\CustomFieldSetEntity;

#[Package('system-settings')]
class CustomFieldSetRelationEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $entityName;

    /**
     * @var string
     */
    protected $customFieldSetId;

    /**
     * @var CustomFieldSetEntity|null
     */
    protected $customFieldSet;

    public function getEntityName(): string
    {
        return $this->entityName;
    }

    public function setEntityName(string $entityName): void
    {
        $this->entityName = $entityName;
    }

    public function getCustomFieldSetId(): string
    {
        return $this->customFieldSetId;
    }

    public function setCustomFieldSetId(string $customFieldSetId): void
    {
        $this->customFieldSetId = $customFieldSetId;
    }

    public function getCustomFieldSet(): ?CustomFieldSetEntity
    {
        return $this->customFieldSet;
    }

    public function setCustomFieldSet(CustomFieldSetEntity $customFieldSet): void
    {
        $this->customFieldSet = $customFieldSet;
    }
}
