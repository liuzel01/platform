<?php declare(strict_types=1);

namespace Shuwei\Administration\Service;

use Shuwei\Administration\Framework\Search\CriteriaCollection;
use Shuwei\Core\Framework\Api\Acl\Role\AclRoleDefinition;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shuwei\Core\Framework\Log\Package;

#[Package('administration')]
class AdminSearcher
{
    /**
     * @internal
     */
    public function __construct(private readonly DefinitionInstanceRegistry $definitionRegistry)
    {
    }

    public function search(CriteriaCollection $entities, Context $context): array
    {
        $result = [];

        foreach ($entities as $entityName => $criteria) {
            if (!$this->definitionRegistry->has($entityName)) {
                continue;
            }

            if (!$context->isAllowed($entityName . ':' . AclRoleDefinition::PRIVILEGE_READ)) {
                continue;
            }

            $repository = $this->definitionRegistry->getRepository($entityName);
            $collection = $repository->search($criteria, $context);

            $result[$entityName] = [
                'data' => $collection->getEntities(),
                'total' => $collection->getTotal(),
            ];
        }

        return $result;
    }
}
