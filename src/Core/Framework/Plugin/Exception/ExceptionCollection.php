<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Plugin\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Shuwei\Core\Framework\Struct\Collection;

/**
 * @extends Collection<ShuweiHttpException>
 */
#[Package('core')]
class ExceptionCollection extends Collection
{
    public function getApiAlias(): string
    {
        return 'plugin_exception_collection';
    }

    protected function getExpectedClass(): ?string
    {
        return ShuweiHttpException::class;
    }
}
