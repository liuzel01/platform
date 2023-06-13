<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Pricing;

use Shuwei\Core\Framework\DataAbstractionLayer\Entity;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
class PriceRuleEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $ruleId;

    /**
     * @var PriceCollection
     */
    protected $price;

    public function getRuleId(): string
    {
        return $this->ruleId;
    }

    public function setRuleId(string $ruleId): void
    {
        $this->ruleId = $ruleId;
    }

    public function getPrice(): PriceCollection
    {
        return $this->price;
    }

    public function setPrice(PriceCollection $price): void
    {
        $this->price = $price;
    }
}
