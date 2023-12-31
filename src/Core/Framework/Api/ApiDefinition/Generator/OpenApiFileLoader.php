<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\ApiDefinition\Generator;

use Shuwei\Core\Framework\Api\ApiDefinition\DefinitionService;
use Shuwei\Core\Framework\Log\Package;
use Symfony\Component\Finder\Finder;

/**
 * @internal
 *
 * @phpstan-import-type OpenApiSpec from DefinitionService
 */
#[Package('core')]
class OpenApiFileLoader
{
    /**
     * @param string[] $paths
     */
    public function __construct(private readonly array $paths)
    {
    }

    /**
     * @return OpenApiSpec
     */
    public function loadOpenapiSpecification(): array
    {
        $spec = [
            'paths' => [],
            'components' => [],
        ];

        if (empty($this->paths)) {
            return $spec;
        }

        $finder = new Finder();
        $finder->in($this->paths)->name('*.json');

        foreach ($finder as $entry) {
            try {
                $data = json_decode((string) file_get_contents($entry->getPathname()), true, \JSON_THROW_ON_ERROR, \JSON_THROW_ON_ERROR);
            } catch (\JsonException $exception) {
                var_dump($entry->getPathname());
            }

            $spec['paths'] = \array_replace_recursive($spec['paths'], $data['paths'] ?? []);
            $spec['components'] = array_merge_recursive(
                $spec['components'],
                $data['components'] ?? []
            );
        }

        return $spec;
    }
}
