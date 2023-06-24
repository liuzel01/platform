<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Adapter\Twig\Extension;

use Shuwei\Core\Framework\Adapter\Twig\TemplateFinder;
use Shuwei\Core\Framework\Adapter\Twig\TemplateScopeDetector;
use Shuwei\Core\Framework\Adapter\Twig\TokenParser\ExtendsTokenParser;
use Shuwei\Core\Framework\Adapter\Twig\TokenParser\IncludeTokenParser;
use Shuwei\Core\Framework\Adapter\Twig\TokenParser\ReturnNodeTokenParser;
use Shuwei\Core\Framework\Log\Package;
use Twig\Extension\AbstractExtension;
use Twig\TokenParser\TokenParserInterface;

#[Package('core')]
class NodeExtension extends AbstractExtension
{
    /**
     * @internal
     */
    public function __construct(
        private readonly TemplateFinder $finder,
        private readonly TemplateScopeDetector $templateScopeDetector,
    )
    {
    }

    /**
     * @return TokenParserInterface[]
     */
    public function getTokenParsers(): array
    {
        return [
            new ExtendsTokenParser($this->finder, $this->templateScopeDetector),
            new IncludeTokenParser($this->finder),
            new ReturnNodeTokenParser(),
        ];
    }

    public function getFinder(): TemplateFinder
    {
        return $this->finder;
    }
}
