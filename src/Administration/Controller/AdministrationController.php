<?php declare(strict_types=1);

namespace Shuwei\Administration\Controller;

use Doctrine\DBAL\Connection;
use Shuwei\Administration\Events\PreResetExcludedSearchTermEvent;
use Shuwei\Administration\Framework\Routing\KnownIps\KnownIpsCollectorInterface;
use Shuwei\Administration\Snippet\SnippetFinderInterface;
use Shuwei\Core\Defaults;
use Shuwei\Core\DevOps\Environment\EnvironmentHelper;
use Shuwei\Core\Framework\Adapter\Twig\TemplateFinder;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shuwei\Core\Framework\Feature;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Routing\Exception\LanguageNotFoundException;
use Shuwei\Core\Framework\Routing\RoutingException;
use Shuwei\Core\Framework\Store\Services\FirstRunWizardService;
use Shuwei\Core\Framework\Util\HtmlSanitizer;
use Shuwei\Core\Framework\Uuid\Uuid;
use Shuwei\Core\Framework\Validation\Exception\ConstraintViolationException;
use Shuwei\Core\PlatformRequest;
use Shuwei\Core\System\SystemConfig\SystemConfigService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route(defaults: ['_routeScope' => ['administration']])]
#[Package('administration')]
class AdministrationController extends AbstractController
{
    private readonly bool $esAdministrationEnabled;

    private readonly bool $esStorefrontEnabled;

    /**
     * @internal
     *
     * @param array<int, int> $supportedApiVersions
     */
    public function __construct(
        private readonly TemplateFinder $finder,
        private readonly FirstRunWizardService $firstRunWizardService,
        private readonly SnippetFinderInterface $snippetFinder,
        private readonly array $supportedApiVersions,
        private readonly KnownIpsCollectorInterface $knownIpsCollector,
        private readonly Connection $connection,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly string $shuweiCoreDir,
        private readonly HtmlSanitizer $htmlSanitizer,
        private readonly DefinitionInstanceRegistry $definitionInstanceRegistry,
        ParameterBagInterface $params,
        private readonly SystemConfigService $systemConfigService
    ) {
        // param is only available if the elasticsearch bundle is enabled
        $this->esAdministrationEnabled = $params->has('elasticsearch.administration.enabled')
            ? $params->get('elasticsearch.administration.enabled')
            : false;
        $this->esStorefrontEnabled = $params->has('elasticsearch.enabled')
            ? $params->get('elasticsearch.enabled')
            : false;
    }

    #[Route(path: '/%shuwei_administration.path_name%', name: 'administration.index', defaults: ['auth_required' => false], methods: ['GET'])]
    public function index(Request $request, Context $context): Response
    {
        $template = $this->finder->find('@Administration/administration/index.html.twig');

        return $this->render($template, [
            'features' => Feature::getAll(),
            'systemLanguageId' => Defaults::LANGUAGE_SYSTEM,
            'defaultLanguageIds' => [Defaults::LANGUAGE_SYSTEM],
            'disableExtensions' => EnvironmentHelper::getVariable('DISABLE_EXTENSIONS', false),
            'liveVersionId' => Defaults::LIVE_VERSION,
            'firstRunWizard' => $this->firstRunWizardService->frwShouldRun(),
            'apiVersion' => $this->getLatestApiVersion(),
            'cspNonce' => $request->attributes->get(PlatformRequest::ATTRIBUTE_CSP_NONCE),
            'adminEsEnable' => $this->esAdministrationEnabled,
            'storefrontEsEnable' => $this->esStorefrontEnabled,
        ]);
    }

    #[Route(path: '/api/_admin/snippets', name: 'api.admin.snippets', methods: ['GET'])]
    public function snippets(Request $request): Response
    {
        $snippets = [];
        $locale = $request->query->get('locale', 'zh-CN');
        $snippets[$locale] = $this->snippetFinder->findSnippets((string) $locale);

        if ($locale !== 'zh-CN') {
            $snippets['zh-CN'] = $this->snippetFinder->findSnippets('zh-CN');
        }

        return new JsonResponse($snippets);
    }

    #[Route(path: '/api/_admin/known-ips', name: 'api.admin.known-ips', methods: ['GET'])]
    public function knownIps(Request $request): Response
    {
        $ips = [];

        foreach ($this->knownIpsCollector->collectIps($request) as $ip => $name) {
            $ips[] = [
                'name' => $name,
                'value' => $ip,
            ];
        }

        return new JsonResponse(['ips' => $ips]);
    }

    #[Route(path: '/api/_admin/sanitize-html', name: 'api.admin.sanitize-html', methods: ['POST'])]
    public function sanitizeHtml(Request $request, Context $context): JsonResponse
    {
        if (!$request->request->has('html')) {
            throw new \InvalidArgumentException('Parameter "html" is missing.');
        }

        $html = (string) $request->request->get('html');
        $field = (string) $request->request->get('field');

        if ($field === '') {
            return new JsonResponse(
                ['preview' => $this->htmlSanitizer->sanitize($html)]
            );
        }

        [$entityName, $propertyName] = explode('.', $field);
        $property = $this->definitionInstanceRegistry->getByEntityName($entityName)->getField($propertyName);

        if ($property === null) {
            throw new \InvalidArgumentException('Invalid field property provided.');
        }

        $flag = $property->getFlag(AllowHtml::class);

        if ($flag === null) {
            return new JsonResponse(
                ['preview' => strip_tags($html)]
            );
        }

        if ($flag instanceof AllowHtml && !$flag->isSanitized()) {
            return new JsonResponse(
                ['preview' => $html]
            );
        }

        return new JsonResponse(
            ['preview' => $this->htmlSanitizer->sanitize($html, [], false, $field)]
        );
    }

    private function fetchLanguageIdByName(string $isoCode, Connection $connection): ?string
    {
        $languageId = $connection->fetchOne(
            '
            SELECT `language`.id FROM `language`
            INNER JOIN locale ON language.translation_code_id = locale.id
            WHERE `code` = :code',
            ['code' => $isoCode]
        );

        return $languageId === false ? null : Uuid::fromBytesToHex($languageId);
    }

    private function getLatestApiVersion(): ?int
    {
        $sortedSupportedApiVersions = array_values($this->supportedApiVersions);

        usort($sortedSupportedApiVersions, fn (int $version1, int $version2) => \version_compare((string) $version1, (string) $version2));

        return array_pop($sortedSupportedApiVersions);
    }
}
