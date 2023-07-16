<?php declare(strict_types=1);

namespace Shuwei\Core\System\Country\Website;

use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\Log\Package;
use Frontend\FrontendContext;
use Symfony\Component\HttpFoundation\Request;

/**
 * This route can be used to load all states of a given country.
 * With this route it is also possible to send the standard API parameters such as: 'page', 'limit', 'filter', etc.
 */
#[Package('system-settings')]
abstract class AbstractCountryStateRoute
{
    abstract public function load(string $countryId, Request $request, Criteria $criteria, FrontendContext $context): CountryStateRouteResponse;

    abstract protected function getDecorated(): AbstractCountryStateRoute;
}
