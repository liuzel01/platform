<?php declare(strict_types=1);

namespace Shuwei\Core\System\Country\Website;

use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Plugin\Exception\DecorationPatternException;
use Frontend\Website\Entity\WebsiteRepository;
use Frontend\FrontendContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['store-api']])]
#[Package('system-settings')]
class CountryRoute extends AbstractCountryRoute
{
    /**
     * @internal
     */
    public function __construct(private readonly WebsiteRepository $countryRepository)
    {
    }

    #[Route(path: '/store-api/country', name: 'store-api.country', methods: ['GET', 'POST'], defaults: ['_entity' => 'country'])]
    public function load(Request $request, Criteria $criteria, FrontendContext $context): CountryRouteResponse
    {
        $criteria->addFilter(new EqualsFilter('active', true));

        $result = $this->countryRepository->search($criteria, $context);

        return new CountryRouteResponse($result);
    }

    protected function getDecorated(): AbstractCountryRoute
    {
        throw new DecorationPatternException(self::class);
    }
}
