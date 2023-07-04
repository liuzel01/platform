<?php declare(strict_types=1);

namespace Shuwei\Core\System\Country\SalesChannel;

use Shuwei\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\Country\Aggregate\CountryState\CountryStateCollection;
use Shuwei\Core\System\SalesChannel\StoreApiResponse;

#[Package('system-settings')]
class CountryStateRouteResponse extends StoreApiResponse
{
    /**
     * @var EntitySearchResult
     */
    protected $object;

    public function __construct(EntitySearchResult $object)
    {
        parent::__construct($object);
    }

    public function getStates(): CountryStateCollection
    {
        /** @var CountryStateCollection $countryStateCollection */
        $countryStateCollection = $this->object->getEntities();

        return $countryStateCollection;
    }
}
