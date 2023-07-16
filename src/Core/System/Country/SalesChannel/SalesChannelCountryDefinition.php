<?php declare(strict_types=1);

namespace Shuwei\Core\System\Country\Website;

use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\Country\CountryDefinition;
use Frontend\Website\Entity\WebsiteDefinitionInterface;
use Frontend\FrontendContext;

#[Package('system-settings')]
class WebsiteCountryDefinition extends CountryDefinition implements WebsiteDefinitionInterface
{
    public function processCriteria(Criteria $criteria, FrontendContext $context): void
    {
        $criteria->addFilter(new EqualsFilter('country.websites.id', $context->getWebsite()->getId()));
    }
}
