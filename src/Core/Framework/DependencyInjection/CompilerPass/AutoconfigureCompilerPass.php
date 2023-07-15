<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DependencyInjection\CompilerPass;

use League\Flysystem\FilesystemOperator;
use Shuwei\Core\Checkout\Cart\CartDataCollectorInterface;
use Shuwei\Core\Checkout\Cart\CartProcessorInterface;
use Shuwei\Core\Checkout\Cart\CartValidatorInterface;
use Shuwei\Core\Checkout\Cart\LineItem\Group\LineItemGroupPackagerInterface;
use Shuwei\Core\Checkout\Cart\LineItem\Group\LineItemGroupSorterInterface;
use Shuwei\Core\Checkout\Cart\LineItemFactoryHandler\LineItemFactoryInterface;
use Shuwei\Core\Checkout\Cart\TaxProvider\AbstractTaxProvider;
use Shuwei\Core\Checkout\Customer\Password\LegacyEncoder\LegacyEncoderInterface;
use Shuwei\Core\Checkout\Document\Renderer\AbstractDocumentRenderer;
use Shuwei\Core\Checkout\Payment\Cart\PaymentHandler\SynchronousPaymentHandlerInterface;
use Shuwei\Core\Checkout\Promotion\Cart\Discount\Filter\FilterPickerInterface;
use Shuwei\Core\Checkout\Promotion\Cart\Discount\Filter\FilterSorterInterface;
use Shuwei\Core\Content\Cms\DataResolver\Element\CmsElementResolverInterface;
use Shuwei\Core\Content\Flow\Dispatching\Storer\FlowStorer;
use Shuwei\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteInterface;
use Shuwei\Core\Content\Sitemap\Provider\AbstractUrlProvider;
use Shuwei\Core\Framework\Adapter\Filesystem\Adapter\AdapterFactoryInterface;
use Shuwei\Core\Framework\Adapter\Twig\NamespaceHierarchy\TemplateNamespaceHierarchyBuilderInterface;
use Shuwei\Core\Framework\DataAbstractionLayer\Dbal\ExceptionHandlerInterface;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shuwei\Core\Framework\DataAbstractionLayer\FieldSerializer\FieldSerializerInterface;
use Shuwei\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexer;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;
use Shuwei\Core\Framework\Routing\AbstractRouteScope;
use Shuwei\Core\Framework\Rule\Rule;
use Shuwei\Core\System\NumberRange\ValueGenerator\Pattern\AbstractValueGenerator;
use Shuwei\Core\System\Website\WebsiteDefinition;
use Shuwei\Core\System\Tax\TaxRuleType\TaxRuleTypeFilterInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

#[Package('core')]
class AutoconfigureCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $container
            ->registerForAutoconfiguration(EntityDefinition::class)
            ->addTag('shuwei.entity.definition');

        $container
            ->registerForAutoconfiguration(AbstractRouteScope::class)
            ->addTag('shuwei.route_scope');

        $container
            ->registerForAutoconfiguration(EntityExtension::class)
            ->addTag('shuwei.entity.extension');

        $container
            ->registerForAutoconfiguration(ScheduledTask::class)
            ->addTag('shuwei.scheduled.task');


        $container
            ->registerForAutoconfiguration(EntityIndexer::class)
            ->addTag('shuwei.entity_indexer');

        $container
            ->registerForAutoconfiguration(ExceptionHandlerInterface::class)
            ->addTag('shuwei.dal.exception_handler');


        $container
            ->registerForAutoconfiguration(FieldSerializerInterface::class)
            ->addTag('shuwei.field_serializer');

        $container
            ->registerForAutoconfiguration(AdapterFactoryInterface::class)
            ->addTag('shuwei.filesystem.factory');

        $container
            ->registerForAutoconfiguration(TemplateNamespaceHierarchyBuilderInterface::class)
            ->addTag('shuwei.twig.hierarchy_builder');

        $container->registerAliasForArgument('shuwei.filesystem.private', FilesystemOperator::class, 'privateFilesystem');
        $container->registerAliasForArgument('shuwei.filesystem.public', FilesystemOperator::class, 'publicFilesystem');
    }
}
