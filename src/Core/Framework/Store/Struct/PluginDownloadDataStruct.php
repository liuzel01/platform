<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Struct;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Struct\Struct;

/**
 * @codeCoverageIgnore
 */
#[Package('merchant-services')]
class PluginDownloadDataStruct extends Struct
{
    /**
     * @var string
     */
    protected $location;

    /**
     * @var string
     */
    protected $type;

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getApiAlias(): string
    {
        return 'store_download_data';
    }
}
