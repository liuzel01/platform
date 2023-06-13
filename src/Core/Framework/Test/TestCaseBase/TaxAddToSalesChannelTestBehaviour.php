<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Test\TestCaseBase;

use Shuwei\Core\System\SalesChannel\SalesChannelContext;
use Shuwei\Core\System\Tax\Aggregate\TaxRule\TaxRuleCollection;
use Shuwei\Core\System\Tax\TaxEntity;

trait TaxAddToSalesChannelTestBehaviour
{
    /**
     * @param array<mixed> $taxData
     */
    protected function addTaxDataToSalesChannel(SalesChannelContext $salesChannelContext, array $taxData): void
    {
        $tax = (new TaxEntity())->assign($taxData);
        $this->addTaxEntityToSalesChannel($salesChannelContext, $tax);
    }

    protected function addTaxEntityToSalesChannel(SalesChannelContext $salesChannelContext, TaxEntity $taxEntity): void
    {
        if ($taxEntity->getRules() === null) {
            $taxEntity->setRules(new TaxRuleCollection());
        }
        $salesChannelContext->getTaxRules()->add($taxEntity);
    }
}
