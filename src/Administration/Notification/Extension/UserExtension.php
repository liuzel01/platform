<?php declare(strict_types=1);

namespace Shuwei\Administration\Notification\Extension;

use Shuwei\Administration\Notification\NotificationDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\User\UserDefinition;

#[Package('administration')]
class UserExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToManyAssociationField('createdNotifications', NotificationDefinition::class, 'created_by_user_id', 'id')
        );
    }

    public function getDefinitionClass(): string
    {
        return UserDefinition::class;
    }
}
