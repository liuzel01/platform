<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\ApiDefinition;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 *
 * @phpstan-import-type Api from DefinitionService
 * @phpstan-import-type ApiType from DefinitionService
 * @phpstan-import-type OpenApiSpec from DefinitionService
 * @phpstan-import-type ApiSchema from DefinitionService
 */
#[Package('core')]
interface ApiDefinitionGeneratorInterface
{
    public function supports(string $format, string $api): bool;

    /**
     * @param array<string, EntityDefinition>|array<string, EntityDefinition> $definitions
     *
     * @phpstan-param Api     $api
     * @phpstan-param ApiType $apiType
     *
     * @return OpenApiSpec
     */
    public function generate(array $definitions, string $api, string $apiType): array;

    /**
     * @param array<string, EntityDefinition>|array<string, EntityDefinition> $definitions
     *
     * @return ApiSchema
     */
    public function getSchema(array $definitions): array;
}
