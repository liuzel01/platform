<?php declare(strict_types=1);

namespace Shuwei\Core\System\SystemConfig;

use Shuwei\Core\Framework\DataAbstractionLayer\Entity;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shuwei\Core\Framework\Log\Package;

#[Package('system-settings')]
class SystemConfigEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $configurationKey;

    /**
     * @var array|bool|float|int|string|null
     */
    protected $configurationValue;

    public function getConfigurationKey(): string
    {
        return $this->configurationKey;
    }

    public function setConfigurationKey(string $configurationKey): void
    {
        $this->configurationKey = $configurationKey;
    }

    /**
     * @return array|bool|float|int|string|null
     */
    public function getConfigurationValue()
    {
        return $this->configurationValue;
    }

    /**
     * @param array|bool|float|int|string|null $configurationValue
     */
    public function setConfigurationValue($configurationValue): void
    {
        $this->configurationValue = $configurationValue;
    }
}
