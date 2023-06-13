<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Adapter\Translation;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Feature;
use Shuwei\Core\Framework\Log\Package;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Service\ResetInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Package('core')]
abstract class AbstractTranslator implements TranslatorInterface, TranslatorBagInterface, LocaleAwareInterface, ResetInterface
{

    public function resetInMemoryCache(): void
    {
        $this->reset();
    }

    /**
     * @param string $cacheDir
     */
    public function warmUp($cacheDir): void
    {
        $this->getDecorated()->warmUp($cacheDir);
    }

    public function reset(): void
    {
        $this->getDecorated()->reset();
    }

    public function resetInjection(): void
    {
        $this->getDecorated()->resetInjection();
    }

    public function injectSettings(string $salesChannelId, string $languageId, string $locale, Context $context): void
    {
        $this->getDecorated()->injectSettings($salesChannelId, $languageId, $locale, $context);
    }

    public function getSnippetSetId(?string $locale = null): ?string
    {
        return $this->getDecorated()->getSnippetSetId($locale);
    }

    abstract public function getDecorated(): AbstractTranslator;

    /**
     * @return mixed|null All kind of data could be cached
     */
    abstract public function trace(string $key, \Closure $param);

    /**
     * @return array<int, string>
     */
    abstract public function getTrace(string $key): array;
}
