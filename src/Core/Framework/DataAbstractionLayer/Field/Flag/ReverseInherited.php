<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag;

use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
class ReverseInherited extends Flag
{
    /**
     * @var string
     */
    protected $propertyName;

    public function __construct(string $propertyName)
    {
        $this->propertyName = $propertyName;
    }

    public function getReversedPropertyName(): string
    {
        return $this->propertyName;
    }

    public function parse(): \Generator
    {
        yield 'reversed_inherited' => $this->propertyName;
    }
}
