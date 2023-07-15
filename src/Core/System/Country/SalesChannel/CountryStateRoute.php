<?php declare(strict_types=1);

namespace Shuwei\Core\System\Country\Website;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shuwei\Core\System\Website\WebsiteContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['store-api']])]
#[Package('system-settings')]
class CountryStateRoute extends AbstractCountryStateRoute
{
    /**
     * @internal
     */
    public function __construct(private readonly EntityRepository $countryStateRepository)
    {
    }

    #[Route(path: '/store-api/country-state/{countryId}', name: 'store-api.country.state', methods: ['GET', 'POST'], defaults: ['_entity' => 'country'])]
    public function load(string $countryId, Request $request, Criteria $criteria, WebsiteContext $context): CountryStateRouteResponse
    {
        $criteria->addFilter(
            new EqualsFilter('countryId', $countryId),
            new EqualsFilter('active', true)
        );
        $criteria->addSorting(new FieldSorting('position', FieldSorting::ASCENDING, true));
        $criteria->addSorting(new FieldSorting('name', FieldSorting::ASCENDING));

        $countryStates = $this->countryStateRepository->search($criteria, $context->getContext());

        return new CountryStateRouteResponse($countryStates);
    }

    protected function getDecorated(): AbstractCountryStateRoute
    {
        throw new DecorationPatternException(self::class);
    }
}
