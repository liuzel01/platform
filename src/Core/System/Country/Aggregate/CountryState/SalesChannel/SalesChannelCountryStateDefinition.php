<?php declare(strict_types=1);

namespace Shuwei\Core\System\Country\Aggregate\CountryState\Website;

use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\Country\Aggregate\CountryState\CountryStateDefinition;
use Shuwei\Core\System\Website\Entity\WebsiteDefinitionInterface;
use Shuwei\Core\System\Website\WebsiteContext;

#[Package('system-settings')]
class WebsiteCountryStateDefinition extends CountryStateDefinition implements WebsiteDefinitionInterface
{
    public function processCriteria(Criteria $criteria, WebsiteContext $context): void
    {
        $criteria->addFilter(
            new EqualsFilter('country_state.country.websites.id', $context->getWebsite()->getId())
        );
    }
}
