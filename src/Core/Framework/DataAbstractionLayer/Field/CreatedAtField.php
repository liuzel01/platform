<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Field;

use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shuwei\Core\Framework\DataAbstractionLayer\FieldSerializer\CreatedAtFieldSerializer;
use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
class CreatedAtField extends DateTimeField
{
    public function __construct()
    {
        parent::__construct('created_at', 'createdAt');
        $this->addFlags(new Required());
    }

    protected function getSerializerClass(): string
    {
        return CreatedAtFieldSerializer::class;
    }
}
