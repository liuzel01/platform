<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Adapter\Twig\Extension;

use Shuwei\Core\Framework\Adapter\Twig\TokenParser\FeatureFlagCallTokenParser;
use Shuwei\Core\Framework\Feature;
use Shuwei\Core\Framework\Log\Package;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

#[Package('core')]
class FeatureFlagExtension extends AbstractExtension
{
    /**
     * @return FeatureFlagCallTokenParser[]
     */
    public function getTokenParsers()
    {
        return [
            new FeatureFlagCallTokenParser(),
        ];
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('feature', $this->feature(...)),
            new TwigFunction('getAllFeatures', $this->getAll(...)),
        ];
    }

    public function feature(string $flag): bool
    {
        return Feature::isActive($flag);
    }

    /**
     * @return array<string, bool>
     */
    public function getAll(): array
    {
        return Feature::getAll();
    }
}
