<?php declare(strict_types=1);

namespace Shuwei\Core\DevOps\Environment;

use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
class EnvironmentHelperTransformerData
{
    /**
     * @param bool|float|int|string|null $value
     * @param bool|float|int|string|null $default
     */
    public function __construct(
        private readonly string $key,
        private $value,
        private $default
    ) {
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return bool|float|int|string|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param bool|float|int|string|null $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @return bool|float|int|string|null
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param bool|float|int|string|null $default
     */
    public function setDefault($default): void
    {
        $this->default = $default;
    }
}
