<?php declare(strict_types=1);

namespace Shuwei\Core\System\Country\Website;

use Shuwei\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\Country\CountryCollection;
use Shuwei\Core\System\Website\StoreApiResponse;

#[Package('system-settings')]
class CountryRouteResponse extends StoreApiResponse
{
    /**
     * @var EntitySearchResult
     */
    protected $object;

    public function __construct(EntitySearchResult $object)
    {
        parent::__construct($object);
    }

    public function getResult(): EntitySearchResult
    {
        return $this->object;
    }

    public function getCountries(): CountryCollection
    {
        /** @var CountryCollection $collection */
        $collection = $this->object->getEntities();

        return $collection;
    }
}
