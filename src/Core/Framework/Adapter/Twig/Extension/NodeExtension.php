<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Adapter\Twig\Extension;

use Shuwei\Core\Framework\Adapter\Twig\TemplateFinder;
use Shuwei\Core\Framework\Adapter\Twig\TokenParser\ExtendsTokenParser;
use Shuwei\Core\Framework\Adapter\Twig\TokenParser\IncludeTokenParser;
use Shuwei\Core\Framework\Adapter\Twig\TokenParser\ReturnNodeTokenParser;
use Shuwei\Core\Framework\Log\Package;
use Twig\Extension\AbstractExtension;

#[Package('core')]
class NodeExtension extends AbstractExtension
{
    /**
     * @internal
     */
    public function __construct(private readonly TemplateFinder $finder)
    {
    }

    public function getTokenParsers(): array
    {
        return [
            new ExtendsTokenParser($this->finder),
            new IncludeTokenParser($this->finder),
            new ReturnNodeTokenParser(),
        ];
    }

    public function getFinder(): TemplateFinder
    {
        return $this->finder;
    }
}
