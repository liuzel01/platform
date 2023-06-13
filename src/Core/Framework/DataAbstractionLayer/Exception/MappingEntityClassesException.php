<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class MappingEntityClassesException extends ShuweiHttpException
{
    public function __construct()
    {
        parent::__construct('Mapping definition neither have entities nor collection.');
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__MAPPING_ENTITY_DEFINITION_CLASSES';
    }
}
