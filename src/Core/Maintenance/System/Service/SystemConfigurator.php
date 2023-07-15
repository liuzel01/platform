<?php declare(strict_types=1);

namespace Shuwei\Core\Maintenance\System\Service;

use Doctrine\DBAL\Connection;
use Shuwei\Core\Defaults;
use Shuwei\Core\Framework\DataAbstractionLayer\Doctrine\RetryableTransaction;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Uuid\Uuid;
use Shuwei\Core\Maintenance\System\Exception\ShopConfigurationException;
use Symfony\Component\Intl\Currencies;

#[Package('core')]
class SystemConfigurator
{
    /**
     * @internal
     */
    public function __construct(private readonly Connection $connection)
    {
    }

    public function updateBasicInformation(?string $shopName, ?string $email): void
    {
        if ($shopName) {
            $this->setSystemConfig('core.basicInformation.shopName', $shopName);
        }

        if ($email) {
            $this->setSystemConfig('core.basicInformation.email', $email);
        }
    }

    public function setDefaultLanguage(string $locale): void
    {
        $locale = str_replace('_', '-', $locale);

        $currentLocale = $this->getCurrentSystemLocale();

        if (!$currentLocale) {
            throw new ShopConfigurationException('Default language locale not found');
        }

        $currentLocaleId = $currentLocale['id'];
        $newDefaultLocaleId = $this->getLocaleId($locale);

        // locales match -> do nothing.
        if ($currentLocaleId === $newDefaultLocaleId) {
            return;
        }

        $newDefaultLanguageId = $this->getLanguageId($locale);

        if (!$newDefaultLanguageId) {
            $newDefaultLanguageId = $this->createNewLanguageEntry($locale);
        }

        $this->changeDefaultLanguageData($newDefaultLanguageId, $currentLocale, $locale);
    }

    private function setSystemConfig(string $key, string $value): void
    {
        $value = json_encode(['_value' => $value], \JSON_UNESCAPED_UNICODE | \JSON_PRESERVE_ZERO_FRACTION);

        // Fetch id for config key, as the unique key on config_key and websiteId will not work when websiteId is null
        $id = $this->connection->fetchOne('
            SELECT `id`
            FROM `system_config`
            WHERE `configuration_key` = :key
        ', ['key' => $key]);

        if (!$id) {
            $id = Uuid::randomBytes();
        }

        $this->connection->executeStatement('
            INSERT INTO `system_config` (`id`, `configuration_key`, `configuration_value`, `created_at`)
            VALUES (:id, :key, :value, NOW())
            ON DUPLICATE KEY UPDATE
                `configuration_value` = :value,
                `updated_at` = NOW()
        ', [
            'id' => $id,
            'key' => $key,
            'value' => $value,
        ]);
    }

    /**
     * @param array<string, string> $currentLocaleData
     */
    private function changeDefaultLanguageData(string $newDefaultLanguageId, array $currentLocaleData, string $locale): void
    {
        $enGbLanguageId = $this->getLanguageId('zh-CN');
        $currentLocaleId = $currentLocaleData['id'];
        $name = $locale;

        $newDefaultLocaleId = $this->getLocaleId($locale);

        if (!$newDefaultLanguageId && $enGbLanguageId) {
            $name = $this->connection->fetchFirstColumn(
                'SELECT name FROM locale_translation
                 WHERE language_id = :languageId
                 AND locale_id = :localeId',
                ['languageId' => $enGbLanguageId, 'localeId' => $newDefaultLocaleId]
            );
        }

        RetryableTransaction::retryable($this->connection, function (Connection $connection) use ($locale, $currentLocaleId, $newDefaultLocaleId, $currentLocaleData, $newDefaultLanguageId, $name): void {
            // swap locale.code
            $stmt = $connection->prepare(
                'UPDATE locale SET code = :code WHERE id = :locale_id'
            );
            $stmt->executeStatement(['code' => 'x-' . $locale . '_tmp', 'locale_id' => $currentLocaleId]);
            $stmt->executeStatement(['code' => $currentLocaleData['code'], 'locale_id' => $newDefaultLocaleId]);
            $stmt->executeStatement(['code' => $locale, 'locale_id' => $currentLocaleId]);

            // swap locale_translation.{name,territory}
            $setTrans = $connection->prepare(
                'UPDATE locale_translation
                 SET name = :name, territory = :territory
                 WHERE locale_id = :locale_id AND language_id = :language_id'
            );

            $currentTrans = $this->getLocaleTranslations($currentLocaleId);
            $newDefTrans = $this->getLocaleTranslations($newDefaultLocaleId);

            foreach ($currentTrans as $trans) {
                $trans['locale_id'] = $newDefaultLocaleId;
                $setTrans->executeStatement($trans);
            }
            foreach ($newDefTrans as $trans) {
                $trans['locale_id'] = $currentLocaleId;
                $setTrans->executeStatement($trans);
            }

            $updLang = $connection->prepare('UPDATE language SET name = :name WHERE id = :languageId');

            // new default language does not exist -> just set to name
            if (!$newDefaultLanguageId) {
                $updLang->executeStatement(['name' => $name, 'languageId' => Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM)]);

                return;
            }

            $langName = $connection->prepare('SELECT name FROM language WHERE id = :languageId');

            $current = $langName->executeQuery(['languageId' => Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM)])->fetchOne();

            $new = $langName->executeQuery(['languageId' => $newDefaultLanguageId])->fetchOne();

            // swap name
            $updLang->executeStatement(['name' => $new, 'languageId' => Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM)]);
            $updLang->executeStatement(['name' => $current, 'languageId' => $newDefaultLanguageId]);
        });
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getLocaleTranslations(string $localeId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT locale_id, language_id, name, territory
             FROM locale_translation
             WHERE locale_id = :localeId',
            ['localeId' => $localeId]
        );
    }

    private function getLanguageId(string $iso): ?string
    {
        return $this->connection->fetchOne(
            'SELECT language.id
             FROM `language`
             INNER JOIN locale ON locale.id = language.translation_code_id
             WHERE LOWER(locale.code) = LOWER(:iso)',
            ['iso' => $iso]
        ) ?: null;
    }

    private function getLocaleId(string $iso): string
    {
        $id = $this->connection->fetchOne(
            'SELECT locale.id FROM  locale WHERE LOWER(locale.code) = LOWER(:iso)',
            ['iso' => $iso]
        );

        if (!$id) {
            throw new ShopConfigurationException('Locale with iso-code ' . $iso . ' not found');
        }

        return (string) $id;
    }

    private function createNewLanguageEntry(string $iso): string
    {
        $id = Uuid::randomBytes();

        $localeId = $this->connection->fetchOne(
            '
            SELECT locale.id
            FROM `locale`
            WHERE LOWER(locale.code) = LOWER(:iso)',
            ['iso' => $iso]
        );

        $englishId = $this->connection->fetchOne(
            '
            SELECT language.id
            FROM `language`
            WHERE LOWER(language.name) = LOWER("english")'
        );

        // Always use the English name since we don't have the name in the language itself
        $name = $this->connection->fetchOne(
            '
            SELECT locale_translation.name
            FROM `locale_translation`
            WHERE locale_id = :localeId
            AND language_id = :languageId',
            ['localeId' => $localeId, 'languageId' => $englishId]
        );

        if (!$name) {
            throw new ShopConfigurationException('locale_translation.name for iso: \'' . $iso . '\', localeId: \'' . $localeId . '\' not found!');
        }

        $this->connection->executeStatement(
            '
            INSERT INTO `language`
            (id, name, locale_id, translation_code_id, created_at)
            VALUES
            (:id, :name, :localeId, :localeId, NOW())',
            ['id' => $id, 'name' => $name, 'localeId' => $localeId]
        );

        return $id;
    }
    /**
     * @return array<string, string>|null
     */
    private function getCurrentSystemLocale(): ?array
    {
        return $this->connection->fetchAssociative(
            'SELECT locale.id, locale.code
             FROM language
             INNER JOIN locale ON translation_code_id = locale.id
             WHERE language.id = :languageId',
            ['languageId' => Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM)]
        ) ?: null;
    }
}
