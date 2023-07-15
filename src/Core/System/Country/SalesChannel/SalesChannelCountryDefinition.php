<?php declare(strict_types=1);

namespace Shuwei\Core\System\Country\Website;

use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\Country\CountryDefinition;
use Shuwei\Core\System\Website\Entity\WebsiteDefinitionInterface;
use Shuwei\Core\System\Website\WebsiteContext;

#[Package('system-settings')]
class WebsiteCountryDefinition extends CountryDefinition implements WebsiteDefinitionInterface
{
    public function processCriteria(Criteria $criteria, WebsiteContext $context): void
    {
        $criteria->addFilter(new EqualsFilter('country.websites.id', $context->getWebsite()->getId()));
    }
}
