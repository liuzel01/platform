<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Plugin\Util;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
class PluginIdProvider
{
    /**
     * @internal
     */
    public function __construct(private readonly EntityRepository $pluginRepo)
    {
    }

    public function getPluginIdByBaseClass(string $pluginBaseClassName, Context $context): string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('baseClass', $pluginBaseClassName));
        /** @var string $id */
        $id = $this->pluginRepo->searchIds($criteria, $context)->firstId();

        return $id;
    }
}
