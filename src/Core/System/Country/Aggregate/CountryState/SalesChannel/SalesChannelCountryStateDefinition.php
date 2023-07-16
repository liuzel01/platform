<?php declare(strict_types=1);

namespace Shuwei\Core\System\Country\Aggregate\CountryState\Website;

use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\Country\Aggregate\CountryState\CountryStateDefinition;
use Frontend\Website\Entity\WebsiteDefinitionInterface;
use Frontend\FrontendContext;

#[Package('system-settings')]
class WebsiteCountryStateDefinition extends CountryStateDefinition implements WebsiteDefinitionInterface
{
    public function processCriteria(Criteria $criteria, FrontendContext $context): void
    {
        $criteria->addFilter(
            new EqualsFilter('country_state.country.websites.id', $context->getWebsite()->getId())
        );
    }
}
