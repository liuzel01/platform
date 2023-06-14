<?php declare(strict_types=1);

namespace Shuwei\Core\Migration\V6_5;

use Doctrine\DBAL\Connection;
use Shuwei\Core\Defaults;
use Shuwei\Core\Framework\DataAbstractionLayer\Doctrine\MultiInsertQueryQueue;
use Shuwei\Core\Framework\Migration\MigrationStep;
use Shuwei\Core\Framework\Uuid\Uuid;

class Migration1686708280BasicData extends MigrationStep
{
    private ?string $enLanguageId = null;

    public function getCreationTimestamp(): int
    {
        return 1686708280;
    }

    public function update(Connection $connection): void
    {
        $hasData = $connection->executeQuery('SELECT 1 FROM `language` LIMIT 1')->fetchAssociative();
        if ($hasData) {
            return;
        }

        $this->createLanguage($connection);
        $this->createLocale($connection);
        $this->createSystemConfig($connection);
    }

    private function createSystemConfig(Connection $connection): void
    {

        $connection->insert('system_config', [
            'id' => Uuid::randomBytes(),
            'configuration_key' => 'core.store.apiUri',
            'configuration_value' => '{"_value": "https://api.58shuwei.com"}',
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }

    private function createLocale(Connection $connection): void
    {
        $localeData = include __DIR__ . '/../../locales.php';
        $queue = new MultiInsertQueryQueue($connection);
        $languageZh = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $languageEn = Uuid::fromHexToBytes($this->getEnLanguageId());

        foreach ($localeData as $locale) {
            if (\in_array($locale['locale'], ['en-US', 'zh-CN'], true)) {
                continue;
            }
            $localeId = Uuid::randomBytes();

            $queue->addInsert(
                'locale',
                ['id' => $localeId, 'code' => $locale['locale'], 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]
            );

            $queue->addInsert(
                'locale_translation',
                [
                    'locale_id' => $localeId,
                    'language_id' => $languageEn,
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                    'name' => $locale['name']['zh-CN'],
                    'territory' => $locale['territory']['zh-CN'],
                ]
            );

            $queue->addInsert(
                'locale_translation',
                [
                    'locale_id' => $localeId,
                    'language_id' => $languageZh,
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                    'name' => $locale['name']['en-US'],
                    'territory' => $locale['territory']['en-US'],
                ]
            );
        }
        $queue->execute();
    }

    private function createLanguage(Connection $connection): void
    {
        $localeEn = Uuid::randomBytes();
        $localeZh = Uuid::randomBytes();
        $languageZh = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $languageEn = Uuid::fromHexToBytes($this->getEnLanguageId());

        $connection->insert('locale', ['id' => $localeEn, 'code' => 'en-US', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $connection->insert('locale', ['id' => $localeZh, 'code' => 'zh-CN', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);

        // second languages
        $connection->insert('language', [
            'id' => $languageEn,
            'name' => 'English',
            'locale_id' => $localeEn,
            'translation_code_id' => $localeEn,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);

        $connection->insert('language', [
            'id' => $languageZh,
            'name' => '中文',
            'locale_id' => $localeZh,
            'translation_code_id' => $localeZh,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);
    }

    private function getEnLanguageId(): string
    {
        if (!$this->enLanguageId) {
            $this->enLanguageId = Uuid::randomHex();
        }

        return $this->enLanguageId;

    }
}
