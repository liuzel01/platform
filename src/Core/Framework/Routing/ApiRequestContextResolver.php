<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Routing;

use Doctrine\DBAL\Connection;
use Shuwei\Core\Checkout\Cart\Price\Struct\CartPrice;
use Shuwei\Core\Defaults;
use Shuwei\Core\Framework\Api\Context\AdminApiSource;
use Shuwei\Core\Framework\Api\Context\ContextSource;
use Shuwei\Core\Framework\Api\Context\SalesChannelApiSource;
use Shuwei\Core\Framework\Api\Context\SystemSource;
use Shuwei\Core\Framework\Api\Exception\MissingPrivilegeException;
use Shuwei\Core\Framework\Api\Util\AccessKeyHelper;
use Shuwei\Core\Framework\App\Exception\AppNotFoundException;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\Pricing\CashRoundingConfig;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Routing\Exception\LanguageNotFoundException;
use Shuwei\Core\Framework\Uuid\Uuid;
use Shuwei\Core\PlatformRequest;
use Symfony\Component\HttpFoundation\Request;

#[Package('core')]
class ApiRequestContextResolver implements RequestContextResolverInterface
{
    use RouteScopeCheckTrait;

    /**
     * @internal
     */
    public function __construct(
        private readonly Connection $connection,
        private readonly RouteScopeRegistry $routeScopeRegistry
    ) {
    }

    public function resolve(Request $request): void
    {
        if ($request->attributes->has(PlatformRequest::ATTRIBUTE_CONTEXT_OBJECT)) {
            return;
        }

        if (!$this->isRequestScoped($request, ApiContextRouteScopeDependant::class)) {
            return;
        }

        $params = $this->getContextParameters($request);
        $languageIdChain = $this->getLanguageIdChain($params);

        $context = new Context(
            $this->resolveContextSource($request),
            [],
            $languageIdChain,
            $params['versionId'] ?? Defaults::LIVE_VERSION,
            $params['considerInheritance'],
        );
        $request->attributes->set(PlatformRequest::ATTRIBUTE_CONTEXT_OBJECT, $context);
    }

    protected function getScopeRegistry(): RouteScopeRegistry
    {
        return $this->routeScopeRegistry;
    }

    /**
     * @return array{currencyId: string, languageId: string, systemFallbackLanguageId: string, currencyFactory: float, currencyPrecision: int, versionId: ?string, considerInheritance: bool}
     */
    private function getContextParameters(Request $request): array
    {
        $params = [
            'languageId' => Defaults::LANGUAGE_SYSTEM,
            'systemFallbackLanguageId' => Defaults::LANGUAGE_SYSTEM,
            'versionId' => $request->headers->get(PlatformRequest::HEADER_VERSION_ID),
            'considerInheritance' => false,
        ];

        $runtimeParams = $this->getRuntimeParameters($request);

        /** @var array{currencyId: string, languageId: string, systemFallbackLanguageId: string, currencyFactory: float, currencyPrecision: int, versionId: ?string, considerInheritance: bool} $params */
        $params = array_replace_recursive($params, $runtimeParams);

        return $params;
    }

    private function getRuntimeParameters(Request $request): array
    {
        $parameters = [];

        if ($request->headers->has(PlatformRequest::HEADER_LANGUAGE_ID)) {
            $langHeader = $request->headers->get(PlatformRequest::HEADER_LANGUAGE_ID);

            if ($langHeader !== null) {
                $parameters['languageId'] = $langHeader;
            }
        }
        return $parameters;
    }

    private function resolveContextSource(Request $request): ContextSource
    {
        if ($userId = $request->attributes->get(PlatformRequest::ATTRIBUTE_OAUTH_USER_ID)) {
            return $this->getAdminApiSource($userId);
        }

        if (!$request->attributes->has(PlatformRequest::ATTRIBUTE_OAUTH_ACCESS_TOKEN_ID)) {
            return new SystemSource();
        }
        return new SystemSource();
    }

    /**
     * @param array{languageId: string, systemFallbackLanguageId: string} $params
     *
     * @return non-empty-array<string>
     */
    private function getLanguageIdChain(array $params): array
    {
        $chain = [$params['languageId']];
        if ($chain[0] === Defaults::LANGUAGE_SYSTEM) {
            return $chain; // no query needed
        }
        // `Context` ignores nulls and duplicates
        $chain[] = $this->getParentLanguageId($chain[0]);
        $chain[] = $params['systemFallbackLanguageId'];

        /** @var non-empty-array<string> $filtered */
        $filtered = array_filter($chain);

        return $filtered;
    }

    private function getParentLanguageId(?string $languageId): ?string
    {
        if ($languageId === null || !Uuid::isValid($languageId)) {
            throw new LanguageNotFoundException($languageId);
        }
        $data = $this->connection->createQueryBuilder()
            ->select(['LOWER(HEX(language.parent_id))'])
            ->from('language')
            ->where('language.id = :id')
            ->setParameter('id', Uuid::fromHexToBytes($languageId))
            ->executeQuery()
            ->fetchFirstColumn();

        if (empty($data)) {
            throw new LanguageNotFoundException($languageId);
        }

        return $data[0];
    }

    private function getAdminApiSource(?string $userId): AdminApiSource
    {
        $source = new AdminApiSource($userId);

        if ($userId !== null) {
            $source->setPermissions($this->fetchPermissions($userId));
            $source->setIsAdmin($this->isAdmin($userId));

            return $source;
        }

        return $source;
    }

    private function isAdmin(string $userId): bool
    {
        return (bool) $this->connection->fetchOne(
            'SELECT admin FROM `user` WHERE id = :id',
            ['id' => Uuid::fromHexToBytes($userId)]
        );
    }

    private function fetchPermissions(string $userId): array
    {
        $permissions = $this->connection->createQueryBuilder()
            ->select(['role.privileges'])
            ->from('acl_user_role', 'mapping')
            ->innerJoin('mapping', 'acl_role', 'role', 'mapping.acl_role_id = role.id')
            ->where('mapping.user_id = :userId')
            ->setParameter('userId', Uuid::fromHexToBytes($userId))
            ->executeQuery()
            ->fetchFirstColumn();

        $list = [];
        foreach ($permissions as $privileges) {
            $privileges = json_decode((string) $privileges, true, 512, \JSON_THROW_ON_ERROR);
            $list = array_merge($list, $privileges);
        }

        return array_unique(array_filter($list));
    }
}
