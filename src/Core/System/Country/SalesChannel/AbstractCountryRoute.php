<?php declare(strict_types=1);

namespace Shuwei\Core\System\Country\Website;

use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\Website\WebsiteContext;
use Symfony\Component\HttpFoundation\Request;

/**
 * This route can be used to load all countries of the authenticated sales channel.
 * With this route it is also possible to send the standard API parameters such as: 'page', 'limit', 'filter', etc.
 */
#[Package('system-settings')]
abstract class AbstractCountryRoute
{
    abstract public function load(Request $request, Criteria $criteria, WebsiteContext $context): CountryRouteResponse;

    abstract protected function getDecorated(): AbstractCountryRoute;
}
