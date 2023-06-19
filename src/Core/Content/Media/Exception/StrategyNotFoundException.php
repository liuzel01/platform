<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('content')]
class StrategyNotFoundException extends ShuweiHttpException
{
    public function __construct(string $strategyName)
    {
        parent::__construct(
            'No Strategy with name "{{ strategyName }}" found.',
            ['strategyName' => $strategyName]
        );
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__MEDIA_STRATEGY_NOT_FOUND';
    }
}
