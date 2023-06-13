<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Search;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\Dbal\EntityDefinitionQueryHelper;
use Shuwei\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shuwei\Core\Framework\DataAbstractionLayer\Exception\ApiProtectionException;
use Shuwei\Core\Framework\DataAbstractionLayer\Exception\RuntimeFieldInCriteriaException;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Field;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\ApiCriteriaAware;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Runtime;
use Shuwei\Core\Framework\Log\Package;

/**
 * @final
 */
#[Package('core')]
class ApiCriteriaValidator
{
    /**
     * @internal
     */
    public function __construct(private readonly DefinitionInstanceRegistry $registry)
    {
    }

    public function validate(string $entity, Criteria $criteria, Context $context): void
    {
        $definition = $this->registry->getByEntityName($entity);

        foreach ($criteria->getAllFields() as $accessor) {
            $fields = EntityDefinitionQueryHelper::getFieldsOfAccessor($definition, $accessor);

            foreach ($fields as $field) {
                if (!$field instanceof Field) {
                    continue;
                }

                if ($field->getFlag(ApiCriteriaAware::class)) {
                    continue;
                }

                /** @var ApiAware|null $flag */
                $flag = $field->getFlag(ApiAware::class);

                if ($flag === null) {
                    throw new ApiProtectionException($accessor);
                }

                if (!$flag->isSourceAllowed($context->getSource()::class)) {
                    throw new ApiProtectionException($accessor);
                }

                /** @var Runtime|null $runtime */
                $runtime = $field->getFlag(Runtime::class);

                if ($runtime !== null) {
                    throw new RuntimeFieldInCriteriaException($accessor);
                }
            }
        }
    }
}
