<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class InconsistentCriteriaIdsException extends ShuweiHttpException
{
    public function __construct()
    {
        parent::__construct('Inconsistent argument for Criteria. Please filter all invalid values first.');
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INCONSISTENT_CRITERIA_IDS';
    }
}
