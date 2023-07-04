<?php declare(strict_types=1);

namespace Shuwei\Core\System\Country\Aggregate\CountryState\SalesChannel;

use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\Country\Aggregate\CountryState\CountryStateDefinition;
use Shuwei\Core\System\SalesChannel\Entity\SalesChannelDefinitionInterface;
use Shuwei\Core\System\SalesChannel\SalesChannelContext;

#[Package('system-settings')]
class SalesChannelCountryStateDefinition extends CountryStateDefinition implements SalesChannelDefinitionInterface
{
    public function processCriteria(Criteria $criteria, SalesChannelContext $context): void
    {
        $criteria->addFilter(
            new EqualsFilter('country_state.country.salesChannels.id', $context->getSalesChannel()->getId())
        );
    }
}
