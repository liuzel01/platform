<?php declare(strict_types=1);

namespace Shuwei\Core\System\SystemConfig\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('system-settings')]
class XmlElementNotFoundException extends ShuweiHttpException
{
    public function __construct(string $element)
    {
        parent::__construct(
            'Unable to locate element with the name "{{ element }}".',
            ['element' => $element]
        );
    }

    public function getErrorCode(): string
    {
        return 'SYSTEM__XML_ELEMENT_NOT_FOUND';
    }
}
