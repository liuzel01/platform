<?php
declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer;

use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
trait EntityIdTrait
{
    /**
     * @var string
     */
    protected $id;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
        $this->_uniqueIdentifier = $id;
    }
}
