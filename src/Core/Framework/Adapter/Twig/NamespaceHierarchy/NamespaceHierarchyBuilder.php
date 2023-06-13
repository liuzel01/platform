<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Adapter\Twig\NamespaceHierarchy;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\Annotation\Concept\ExtensionPattern\HandlerChain;

/**
 * @HandlerChain(
 *     serviceTag="shuwei.twig.hierarchy_builder",
 *     handlerInterface="TemplateNamespaceHierarchyBuilderInterface"
 * )
 */
#[Package('core')]
class NamespaceHierarchyBuilder
{
    /**
     * @internal
     *
     * @param TemplateNamespaceHierarchyBuilderInterface[] $namespaceHierarchyBuilders
     */
    public function __construct(private readonly iterable $namespaceHierarchyBuilders)
    {
    }

    public function buildHierarchy(): array
    {
        $hierarchy = [];

        foreach ($this->namespaceHierarchyBuilders as $hierarchyBuilder) {
            $hierarchy = $hierarchyBuilder->buildNamespaceHierarchy($hierarchy);
        }

        return $hierarchy;
    }
}
