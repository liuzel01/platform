<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer;

use Shuwei\Core\Defaults;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Struct\Struct;

#[Package('core')]
class TaxFreeConfig extends Struct
{
    public function __construct(
        protected bool $enabled = false,
        protected string $currencyId = Defaults::CURRENCY,
        protected float $amount = 0
    ) {
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getCurrencyId(): string
    {
        return $this->currencyId;
    }

    public function setCurrencyId(string $currencyId): void
    {
        $this->currencyId = $currencyId;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }
}
