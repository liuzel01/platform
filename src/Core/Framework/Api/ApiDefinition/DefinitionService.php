<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\ApiDefinition;

use Shuwei\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\Log\Package;

/**
 * @phpstan-type Api DefinitionService::API|DefinitionService::STORE_API
 * @phpstan-type ApiType DefinitionService::TypeJsonApi|DefinitionService::TypeJson
 * @phpstan-type OpenApiSpec  array{paths: array<string,array<mixed>>, components: array<mixed>}
 * @phpstan-type ApiSchema array<string, array{name: string, translatable: list<string>, properties: array<string, mixed>}|array{entity: string, properties: array<string, mixed>, write-protected: bool, read-protected: bool}>
 */
#[Package('core')]
class DefinitionService
{
    final public const API = 'api';
    final public const STORE_API = 'store-api';

    /**
     * @deprecated tag:v6.6.0 - Will be removed. Use DefinitionService::TYPE_JSON_API instead
     *
     * @phpstan-ignore-next-line ignore needs to be removed when deprecation is removed
     */
    final public const TypeJsonApi = self::TYPE_JSON_API;

    final public const TYPE_JSON_API = 'jsonapi';

    /**
     * @deprecated tag:v6.6.0 - Will be removed. Use DefinitionService::TYPE_JSON instead
     *
     * @phpstan-ignore-next-line ignore needs to be removed when deprecation is removed
     */
    final public const TypeJson = self::TYPE_JSON;

    final public const TYPE_JSON = 'json';

    /**
     * @var ApiDefinitionGeneratorInterface[]
     */
    private readonly array $generators;

    /**
     * @internal
     */
    public function __construct(
        private readonly DefinitionInstanceRegistry $definitionRegistry,
        ApiDefinitionGeneratorInterface ...$generators
    ) {
        $this->generators = $generators;
    }

    /**
     * @param string $format
     * @param string $type
     * @param string $apiType
     * @return array
     */
    public function generate(string $format = 'openapi-3', string $type = self::API, string $apiType = self::TYPE_JSON_API): array
    {
        return $this->getGenerator($format, $type)->generate($this->getDefinitions($type), $type, $apiType);
    }

    /**
     * @param string $format
     * @param string $type
     * @return array
     */
    public function getSchema(string $format = 'openapi-3', string $type = self::API): array
    {
        return $this->getGenerator($format, $type)->getSchema($this->getDefinitions($type));
    }

    /**
     * @return ApiType|null
     */
    public function toApiType(string $apiType): ?string
    {
        if ($apiType !== self::TYPE_JSON_API && $apiType !== self::TYPE_JSON) {
            return null;
        }

        return $apiType;
    }

    /**
     * @throws ApiDefinitionGeneratorNotFoundException
     */
    private function getGenerator(string $format, string $type): ApiDefinitionGeneratorInterface
    {
        foreach ($this->generators as $generator) {
            if ($generator->supports($format, $type)) {
                return $generator;
            }
        }

        throw new ApiDefinitionGeneratorNotFoundException($format);
    }

    /**
     * @throws ApiDefinitionGeneratorNotFoundException
     *
     * @return array<string, EntityDefinition>|list<EntityDefinition>
     */
    private function getDefinitions(string $type): array
    {
        if ($type === self::API) {
            return $this->definitionRegistry->getDefinitions();
        }
        throw new ApiDefinitionGeneratorNotFoundException($type);
    }
}
