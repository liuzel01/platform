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
use Shuwei\Core\System\SalesChannel\SalesChannelDefinition;
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
            ->registerForAutoconfiguration(SalesChannelDefinition::class)
            ->addTag('shuwei.sales_channel.entity.definition');

        $container
            ->registerForAutoconfiguration(AbstractRouteScope::class)
            ->addTag('shuwei.route_scope');

        $container
            ->registerForAutoconfiguration(EntityExtension::class)
            ->addTag('shuwei.entity.extension');

        $container
            ->registerForAutoconfiguration(CartProcessorInterface::class)
            ->addTag('shuwei.cart.processor');

        $container
            ->registerForAutoconfiguration(CartDataCollectorInterface::class)
            ->addTag('shuwei.cart.collector');

        $container
            ->registerForAutoconfiguration(ScheduledTask::class)
            ->addTag('shuwei.scheduled.task');

        $container
            ->registerForAutoconfiguration(CartValidatorInterface::class)
            ->addTag('shuwei.cart.validator');

        $container
            ->registerForAutoconfiguration(LineItemFactoryInterface::class)
            ->addTag('shuwei.cart.line_item.factory');

        $container
            ->registerForAutoconfiguration(LineItemGroupPackagerInterface::class)
            ->addTag('lineitem.group.packager');

        $container
            ->registerForAutoconfiguration(LineItemGroupSorterInterface::class)
            ->addTag('lineitem.group.sorter');

        $container
            ->registerForAutoconfiguration(LegacyEncoderInterface::class)
            ->addTag('shuwei.legacy_encoder');

        $container
            ->registerForAutoconfiguration(EntityIndexer::class)
            ->addTag('shuwei.entity_indexer');

        $container
            ->registerForAutoconfiguration(ExceptionHandlerInterface::class)
            ->addTag('shuwei.dal.exception_handler');

        $container
            ->registerForAutoconfiguration(AbstractDocumentRenderer::class)
            ->addTag('document.renderer');

        $container
            ->registerForAutoconfiguration(SynchronousPaymentHandlerInterface::class)
            ->addTag('shuwei.payment.method.sync');

        $container
            ->registerForAutoconfiguration(FilterSorterInterface::class)
            ->addTag('promotion.filter.sorter');

        $container
            ->registerForAutoconfiguration(FilterPickerInterface::class)
            ->addTag('promotion.filter.picker');

        $container
            ->registerForAutoconfiguration(Rule::class)
            ->addTag('shuwei.rule.definition');

        $container
            ->registerForAutoconfiguration(AbstractTaxProvider::class)
            ->addTag('shuwei.tax.provider');

        $container
            ->registerForAutoconfiguration(CmsElementResolverInterface::class)
            ->addTag('shuwei.cms.data_resolver');

        $container
            ->registerForAutoconfiguration(FieldSerializerInterface::class)
            ->addTag('shuwei.field_serializer');

        $container
            ->registerForAutoconfiguration(FlowStorer::class)
            ->addTag('flow.storer');

        $container
            ->registerForAutoconfiguration(AbstractUrlProvider::class)
            ->addTag('shuwei.sitemap_url_provider');

        $container
            ->registerForAutoconfiguration(AdapterFactoryInterface::class)
            ->addTag('shuwei.filesystem.factory');

        $container
            ->registerForAutoconfiguration(AbstractValueGenerator::class)
            ->addTag('shuwei.value_generator_pattern');

        $container
            ->registerForAutoconfiguration(TaxRuleTypeFilterInterface::class)
            ->addTag('tax.rule_type_filter');

        $container
            ->registerForAutoconfiguration(SeoUrlRouteInterface::class)
            ->addTag('shuwei.seo_url.route');

        $container
            ->registerForAutoconfiguration(TemplateNamespaceHierarchyBuilderInterface::class)
            ->addTag('shuwei.twig.hierarchy_builder');

        $container->registerAliasForArgument('shuwei.filesystem.private', FilesystemOperator::class, 'privateFilesystem');
        $container->registerAliasForArgument('shuwei.filesystem.public', FilesystemOperator::class, 'publicFilesystem');
    }
}
