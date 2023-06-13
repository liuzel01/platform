<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\MessageQueue\ScheduledTask;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shuwei\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shuwei\Core\Framework\Feature;
use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
class ScheduledTaskDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'scheduled_task';

    final public const STATUS_SCHEDULED = 'scheduled';

    final public const STATUS_QUEUED = 'queued';

    final public const STATUS_SKIPPED = 'skipped';

    final public const STATUS_RUNNING = 'running';

    final public const STATUS_FAILED = 'failed';

    final public const STATUS_INACTIVE = 'inactive';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return ScheduledTaskCollection::class;
    }

    public function getEntityClass(): string
    {
        return ScheduledTaskEntity::class;
    }

    public function getDefaults(): array
    {
        return ['nextExecutionTime' => new \DateTime()];
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function defineFields(): FieldCollection
    {
        $fields = new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new StringField('name', 'name'))->addFlags(new Required()),
            (new StringField('scheduled_task_class', 'scheduledTaskClass', 512))->addFlags(new Required()),
            (new IntField('run_interval', 'runInterval', 0))->addFlags(new Required()),
            (new StringField('status', 'status'))->addFlags(new Required()),
            new DateTimeField('last_execution_time', 'lastExecutionTime'),
            (new DateTimeField('next_execution_time', 'nextExecutionTime'))->addFlags(new Required()),
        ]);

        // defaultRunInterval will be required in v6.6.0.0
        if (Feature::isActive('v6.6.0.0')) {
            $fields->add((new IntField('default_run_interval', 'defaultRunInterval', 0))->addFlags(new Required()));
        } else {
            $fields->add(new IntField('default_run_interval', 'defaultRunInterval', 0));
        }

        return $fields;
    }
}
