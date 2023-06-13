<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Struct;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Struct\Struct;

/**
 * @codeCoverageIgnore
 */
#[Package('merchant-services')]
class StoreLicenseTypeStruct extends Struct
{
    /**
     * @var string
     */
    protected $name;

    public function getApiAlias(): string
    {
        return 'store_license_type';
    }
}
