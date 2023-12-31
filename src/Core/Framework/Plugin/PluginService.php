<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Plugin;

use Composer\IO\IOInterface;
use Composer\Package\CompletePackageInterface;
use Shuwei\Core\Defaults;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Plugin\Changelog\ChangelogService;
use Shuwei\Core\Framework\Plugin\Exception\ExceptionCollection;
use Shuwei\Core\Framework\Plugin\Exception\PluginChangelogInvalidException;
use Shuwei\Core\Framework\Plugin\Exception\PluginComposerJsonInvalidException;
use Shuwei\Core\Framework\Plugin\Exception\PluginNotFoundException;
use Shuwei\Core\Framework\Plugin\Util\PluginFinder;
use Shuwei\Core\Framework\Plugin\Util\VersionSanitizer;
use Shuwei\Core\Framework\ShuweiHttpException;
use Shuwei\Core\System\Language\LanguageEntity;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @internal
 */
#[Package('core')]
class PluginService
{
    final public const COMPOSER_AUTHOR_ROLE_MANUFACTURER = 'Manufacturer';

    public function __construct(
        private readonly string $pluginDir,
        private readonly string $projectDir,
        private readonly EntityRepository $pluginRepo,
        private readonly EntityRepository $languageRepo,
        private readonly ChangelogService $changelogService,
        private readonly PluginFinder $pluginFinder,
        private readonly VersionSanitizer $versionSanitizer
    ) {
    }

    public function refreshPlugins(Context $shuweiContext, IOInterface $composerIO): ExceptionCollection
    {
        $errors = new ExceptionCollection();
        $pluginsFromFileSystem = $this->pluginFinder->findPlugins($this->pluginDir, $this->projectDir, $errors, $composerIO);

        $installedPlugins = $this->getPlugins(new Criteria(), $shuweiContext);

        $plugins = [];
        foreach ($pluginsFromFileSystem as $pluginFromFileSystem) {
            $baseClass = $pluginFromFileSystem->getBaseClass();
            $pluginPath = $pluginFromFileSystem->getPath();
            $info = $pluginFromFileSystem->getComposerPackage();

            $autoload = $info->getAutoload();
            if (empty($autoload) || (empty($autoload['psr-4']) && empty($autoload['psr-0']))) {
                $errors->add(new PluginComposerJsonInvalidException(
                    $pluginPath . '/composer.json',
                    ['Neither a PSR-4 nor PSR-0 autoload information is given.']
                ));

                continue;
            }

            $pluginVersion = $this->versionSanitizer->sanitizePluginVersion($info->getVersion());
            $extra = $info->getExtra();
            $license = $info->getLicense();
            $pluginIconPath = $extra['plugin-icon'] ?? 'src/Resources/config/plugin.png';

            $pluginData = [
                'name' => $pluginFromFileSystem->getName(),
                'baseClass' => $baseClass,
                'composerName' => $info->getName(),
                'path' => (new Filesystem())->makePathRelative($pluginPath, $this->projectDir),
                'author' => $this->getAuthors($info),
                'copyright' => $extra['copyright'] ?? null,
                'license' => implode(', ', $license),
                'version' => $pluginVersion,
                'iconRaw' => $this->getPluginIconRaw($pluginPath . '/' . $pluginIconPath),
                'autoload' => $info->getAutoload(),
                'managedByComposer' => $pluginFromFileSystem->getManagedByComposer(),
            ];

            $pluginData['translations'] = $this->getTranslations($shuweiContext, $extra);

            if ($changelogFiles = $this->changelogService->getChangelogFiles($pluginPath)) {
                foreach ($changelogFiles as $file) {
                    $languageId = $this->getLanguageIdForLocale(
                        $this->changelogService->getLocaleFromChangelogFile($file),
                        $shuweiContext
                    );
                    if ($languageId === '') {
                        continue;
                    }

                    try {
                        $pluginData['translations'][$languageId]['changelog'] = $this->changelogService->parseChangelog($file);
                    } catch (PluginChangelogInvalidException $changelogInvalidException) {
                        $errors->add($changelogInvalidException);
                    }
                }
            }

            /** @var PluginEntity $currentPluginEntity */
            $currentPluginEntity = $installedPlugins->filterByProperty('baseClass', $baseClass)->first();
            if ($currentPluginEntity !== null) {
                $currentPluginId = $currentPluginEntity->getId();
                $pluginData['id'] = $currentPluginId;

                $currentPluginVersion = $currentPluginEntity->getVersion();
                if (!$currentPluginEntity->getInstalledAt()) {
                    $pluginData['version'] = $pluginVersion;
                    $pluginData['upgradeVersion'] = null;
                } elseif ($this->hasPluginUpdate($pluginVersion, $currentPluginVersion)) {
                    $pluginData['version'] = $currentPluginVersion;
                    $pluginData['upgradeVersion'] = $pluginVersion;
                } else {
                    $pluginData['upgradeVersion'] = null;
                }

                $installedPlugins->remove($currentPluginId);
            }

            $plugins[] = $pluginData;
        }

        if ($plugins !== []) {
            foreach ($plugins as $plugin) {
                try {
                    $this->pluginRepo->upsert([$plugin], $shuweiContext);
                } catch (ShuweiHttpException $exception) {
                    $errors->set($plugin['name'], $exception);
                }
            }
        }

        // delete plugins, which are in storage but not in filesystem anymore
        $deletePluginIds = $installedPlugins->getIds();
        if (\count($deletePluginIds) !== 0) {
            $deletePlugins = [];
            foreach ($deletePluginIds as $deletePluginId) {
                $deletePlugins[] = ['id' => $deletePluginId];
            }
            $this->pluginRepo->delete($deletePlugins, $shuweiContext);
        }

        return $errors;
    }

    /**
     * @throws PluginNotFoundException
     */
    public function getPluginByName(string $pluginName, Context $context): PluginEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', $pluginName));

        $pluginEntity = $this->getPlugins($criteria, $context)->first();
        if ($pluginEntity === null) {
            throw new PluginNotFoundException($pluginName);
        }

        return $pluginEntity;
    }

    private function getPlugins(Criteria $criteria, Context $context): PluginCollection
    {
        /** @var PluginCollection $pluginCollection */
        $pluginCollection = $this->pluginRepo->search($criteria, $context)->getEntities();

        return $pluginCollection;
    }

    private function hasPluginUpdate(string $updateVersion, string $currentVersion): bool
    {
        return version_compare($updateVersion, $currentVersion, '>');
    }

    private function getLanguageIdForLocale(string $locale, Context $context): string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('language.translationCode.code', $locale));
        $result = $this->languageRepo->search($criteria, $context);

        if ($result->getTotal() === 0) {
            return '';
        }

        /** @var LanguageEntity $languageEntity */
        $languageEntity = $result->first();

        return $languageEntity->getId();
    }

    private function getPluginIconRaw(string $pluginIconPath): ?string
    {
        if (!is_file($pluginIconPath)) {
            return null;
        }

        $rawContent = file_get_contents($pluginIconPath);

        if (!\is_string($rawContent)) {
            return null;
        }

        return $rawContent;
    }

    private function getAuthors(CompletePackageInterface $info): string
    {
        $composerAuthors = $info->getAuthors();

        $manufacturerAuthors = array_filter($composerAuthors, static fn (array $author): bool => ($author['role'] ?? '') === self::COMPOSER_AUTHOR_ROLE_MANUFACTURER);

        if (empty($manufacturerAuthors)) {
            $manufacturerAuthors = $composerAuthors;
        }

        $authorNames = array_column($manufacturerAuthors, 'name');

        return implode(', ', $authorNames);
    }

    /**
     * @param array<string, mixed> $extra
     *
     * @return array<string, array<string, string>>
     */
    private function getTranslations(Context $context, array $extra): array
    {
        $properties = ['label', 'description', 'manufacturerLink', 'supportLink'];

        $localeMapping = [];
        $translations = [];

        /*
         * @example payload
         * {
         *     "shuwei-plugin-class":"Swag\\MyDemoData\\MyDemoData",
         *     "label":{
         *         "en-US":"Label für das Plugin MyDemoData",
         *         "zh-CN":"Label for the plugin MyDemoData"
         *     },
         *     "description":{
         *         "en-US":"Beschreibung für das Plugin MyDemoData",
         *         "zh-CN":"Description for the plugin MyDemoData"
         *     }
         * }
         */
        foreach ($extra as $property => $propertyTranslations) {
            if (!\in_array($property, $properties, true)) {
                continue;
            }

            foreach ($propertyTranslations as $locale => $translation) {
                $languageId = $this->getLanguageIdForLocale($locale, $context);

                // build a mapping based on locales, which is used for translation fallback later
                $localeMapping[$locale][$property] = $translation;

                if ($languageId === '') {
                    continue;
                }
                $translations[$languageId][$property] = $translation;
            }
        }

        // validate that the plugin is translated for the system language
        if (isset($translations[Defaults::LANGUAGE_SYSTEM])) {
            return $translations;
        }

        // if the plugin has no system translation, check if zh-CN can be used as fallback
        if (isset($localeMapping['zh-CN'])) {
            $translations[Defaults::LANGUAGE_SYSTEM] = $localeMapping['zh-CN'];

            return $translations;
        }

        // if the plugin has no translation for en-gb, use the first translation of the plugin as default translation
        if (!isset($translations[Defaults::LANGUAGE_SYSTEM])) {
            $translations[Defaults::LANGUAGE_SYSTEM] = array_values($localeMapping)[0];
        }

        return $translations;
    }
}
