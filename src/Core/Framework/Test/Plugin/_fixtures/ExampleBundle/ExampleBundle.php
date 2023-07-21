<?php declare(strict_types=1);

namespace Shuwei\Tests\Unit\Core\Framework\Plugin\_fixtures\ExampleBundle;

use Shuwei\Core\Framework\Parameter\AdditionalBundleParameters;
use Shuwei\Core\Framework\Plugin;
use Shuwei\Tests\Unit\Core\Framework\Plugin\_fixtures\ExampleBundle\FeatureA\FeatureA;
use Shuwei\Tests\Unit\Core\Framework\Plugin\_fixtures\ExampleBundle\FeatureB\FeatureB;

/**
 * @internal
 */
class ExampleBundle extends Plugin
{
    public function getAdditionalBundles(AdditionalBundleParameters $parameters): array
    {
        return [
            new FeatureA(),
            new FeatureB(),
        ];
    }
}
