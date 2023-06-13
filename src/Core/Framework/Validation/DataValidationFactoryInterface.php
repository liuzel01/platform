<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Validation;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\Annotation\Concept\DeprecationPattern\ReplaceDecoratedInterface;
use Shuwei\Core\System\SalesChannel\SalesChannelContext;

/**
 * @ReplaceDecoratedInterface(
 *     deprecatedInterface="ValidationServiceInterface",
 *     replacedBy="DataValidationFactoryInterface"
 * )
 */
#[Package('core')]
interface DataValidationFactoryInterface
{
    public function create(SalesChannelContext $context): DataValidationDefinition;

    public function update(SalesChannelContext $context): DataValidationDefinition;
}
