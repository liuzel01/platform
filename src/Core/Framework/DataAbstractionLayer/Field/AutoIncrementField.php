<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Field;

use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\WriteProtected;
use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
class AutoIncrementField extends IntField
{
    public function __construct()
    {
        parent::__construct('auto_increment', 'autoIncrement');

        $this->addFlags(new WriteProtected());
    }
}
