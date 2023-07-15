<?php declare(strict_types=1);

namespace Shuwei\Core\System\SystemConfig\Service;

use Shuwei\Core\Framework\Bundle;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shuwei\Core\Framework\Feature;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\SystemConfig\Exception\BundleConfigNotFoundException;
use Shuwei\Core\System\SystemConfig\Exception\ConfigurationNotFoundException;
use Shuwei\Core\System\SystemConfig\SystemConfigService;
use Shuwei\Core\System\SystemConfig\Util\ConfigReader;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

#[Package('system-settings')]
class ConfigurationService
{
    /**
     * @internal
     *
     * @param BundleInterface[] $bundles
     */
    public function __construct(
        private readonly iterable $bundles,
        private readonly ConfigReader $configReader,
        private readonly SystemConfigService $systemConfigService
    ) {
    }

    /**
     * @throws ConfigurationNotFoundException
     * @throws \InvalidArgumentException
     * @throws BundleConfigNotFoundException
     *
     * @return array<mixed>
     */
    public function getConfiguration(string $domain, Context $context): array
    {
        $validDomain = preg_match('/^([\w-]+)\.?([\w-]*)$/', $domain, $match);

        if (!$validDomain) {
            throw new \InvalidArgumentException('Expected domain');
        }

        $scope = $match[1];
        $configName = $match[2] !== '' ? $match[2] : null;

        $config = $this->fetchConfiguration($scope === 'core' ? 'System' : $scope, $configName, $context);
        if (!$config) {
            throw new ConfigurationNotFoundException($scope);
        }

        $domain = rtrim($domain, '.') . '.';

        foreach ($config as $i => $card) {
            if (\array_key_exists('flag', $card) && !Feature::isActive($card['flag'])) {
                unset($config[$i]);

                continue;
            }

            foreach ($card['elements'] ?? [] as $j => $field) {
                $newField = ['name' => $domain . $field['name']];

                if (\array_key_exists('flag', $field) && !Feature::isActive($field['flag'])) {
                    unset($card['elements'][$j]);

                    continue;
                }

                if (\array_key_exists('type', $field)) {
                    $newField['type'] = $field['type'];
                }

                unset($field['type'], $field['name']);
                $newField['config'] = $field;
                $card['elements'][$j] = $newField;
            }
            $config[$i] = $card;
        }

        return $config;
    }

    /**
     * @return array<mixed>
     */
    public function getResolvedConfiguration(string $domain, Context $context, ?string $websiteId = null): array
    {
        $config = [];
        if ($this->checkConfiguration($domain, $context)) {
            $config = array_merge(
                $config,
                $this->enrichValues(
                    $this->getConfiguration($domain, $context),
                    $websiteId
                )
            );
        }

        return $config;
    }

    public function checkConfiguration(string $domain, Context $context): bool
    {
        try {
            $this->getConfiguration($domain, $context);

            return true;
        } catch (\InvalidArgumentException|ConfigurationNotFoundException|BundleConfigNotFoundException) {
            return false;
        }
    }

    /**
     * @return array<mixed>|null
     */
    private function fetchConfiguration(string $scope, ?string $configName, Context $context): ?array
    {
        $technicalName = \array_slice(explode('\\', $scope), -1)[0];

        foreach ($this->bundles as $bundle) {
            if ($bundle->getName() === $technicalName && $bundle instanceof Bundle) {
                return $this->configReader->getConfigFromBundle($bundle, $configName);
            }
        }
        return null;
    }


    /**
     * @param array<mixed> $config
     *
     * @return array<mixed>
     */
    private function enrichValues(array $config, ?string $websiteId): array
    {
        foreach ($config as &$card) {
            if (!\is_array($card['elements'] ?? false)) {
                continue;
            }

            foreach ($card['elements'] as &$element) {
                $element['value'] = $this->systemConfigService->get(
                    $element['name'],
                    $websiteId
                ) ?? $element['config']['defaultValue'] ?? '';
            }
        }

        return $config;
    }
}
