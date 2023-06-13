<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DependencyInjection\CompilerPass;

use Shuwei\Core\Content\Flow\Dispatching\Storer\ConfirmUrlStorer;
use Shuwei\Core\Content\Flow\Dispatching\Storer\ContactFormDataStorer;
use Shuwei\Core\Content\Flow\Dispatching\Storer\ContentsStorer;
use Shuwei\Core\Content\Flow\Dispatching\Storer\ContextTokenStorer;
use Shuwei\Core\Content\Flow\Dispatching\Storer\DataStorer;
use Shuwei\Core\Content\Flow\Dispatching\Storer\EmailStorer;
use Shuwei\Core\Content\Flow\Dispatching\Storer\NameStorer;
use Shuwei\Core\Content\Flow\Dispatching\Storer\RecipientsStorer;
use Shuwei\Core\Content\Flow\Dispatching\Storer\ResetUrlStorer;
use Shuwei\Core\Content\Flow\Dispatching\Storer\ReviewFormDataStorer;
use Shuwei\Core\Content\Flow\Dispatching\Storer\ShopNameStorer;
use Shuwei\Core\Content\Flow\Dispatching\Storer\SubjectStorer;
use Shuwei\Core\Content\Flow\Dispatching\Storer\TemplateDataStorer;
use Shuwei\Core\Content\Flow\Dispatching\Storer\UrlStorer;
use Shuwei\Core\Framework\Log\Package;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

#[Package('business-ops')]
class RemoveOldFlowStorerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $deprecated = [
            ResetUrlStorer::class,
            RecipientsStorer::class,
            ContextTokenStorer::class,
            NameStorer::class,
            DataStorer::class,
            ContactFormDataStorer::class,
            ContentsStorer::class,
            ConfirmUrlStorer::class,
            ReviewFormDataStorer::class,
            EmailStorer::class,
            UrlStorer::class,
            TemplateDataStorer::class,
            SubjectStorer::class,
            ShopNameStorer::class,
        ];

        foreach ($deprecated as $serviceId) {
            $this->removeTag($container, $serviceId);
        }
    }

    private function removeTag(ContainerBuilder $container, string $serviceId): void
    {
        if (!$container->hasDefinition($serviceId)) {
            return;
        }

        $definition = $container->getDefinition($serviceId);

        if (!$definition->hasTag('flow.storer')) {
            return;
        }

        $definition->clearTag('flow.storer');
    }
}
