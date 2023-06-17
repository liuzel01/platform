<?php declare(strict_types=1);

namespace Shuwei\Core\Migration\V6_5;

use Doctrine\DBAL\Connection;
use Shuwei\Core\Defaults;
use Shuwei\Core\Framework\DataAbstractionLayer\Doctrine\MultiInsertQueryQueue;
use Shuwei\Core\Framework\Migration\MigrationStep;
use Shuwei\Core\Framework\Uuid\Uuid;

class Migration1686995880BasicData extends MigrationStep
{
    private ?string $enLanguageId = null;

    public function getCreationTimestamp(): int
    {
        return 1686995880;
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
        $this->createDefaultSnippetSets($connection);
        $this->createDefaultMediaFolders($connection);
    }

    private function createDefaultMediaFolders(Connection $connection): void
    {
        $queue = new MultiInsertQueryQueue($connection);

        $queue->addInsert('media_default_folder', ['id' => Uuid::randomBytes(), 'association_fields' => '["productMedia"]', 'entity' => 'product', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $queue->addInsert('media_default_folder', ['id' => Uuid::randomBytes(), 'association_fields' => '["productManufacturers"]', 'entity' => 'product_manufacturer', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $queue->addInsert('media_default_folder', ['id' => Uuid::randomBytes(), 'association_fields' => '["avatarUser"]', 'entity' => 'user', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $queue->addInsert('media_default_folder', ['id' => Uuid::randomBytes(), 'association_fields' => '["mailTemplateMedia"]', 'entity' => 'mail_template', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $queue->addInsert('media_default_folder', ['id' => Uuid::randomBytes(), 'association_fields' => '["categories"]', 'entity' => 'category', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $queue->addInsert('media_default_folder', ['id' => Uuid::randomBytes(), 'association_fields' => '[]', 'entity' => 'cms_page', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $queue->addInsert('media_default_folder', ['id' => Uuid::randomBytes(), 'association_fields' => '["documents"]', 'entity' => 'document', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $queue->execute();

        $notCreatedDefaultFolders = $connection->executeQuery('
            SELECT `media_default_folder`.`id` default_folder_id, `media_default_folder`.`entity` entity
            FROM `media_default_folder`
                LEFT JOIN `media_folder` ON `media_folder`.`default_folder_id` = `media_default_folder`.`id`
            WHERE `media_folder`.`id` IS NULL
        ')->fetchAllAssociative();

        foreach ($notCreatedDefaultFolders as $notCreatedDefaultFolder) {
            $this->createDefaultFolder(
                $connection,
                $notCreatedDefaultFolder['default_folder_id'],
                $notCreatedDefaultFolder['entity']
            );
        }
    }
    private function createDefaultFolder(Connection $connection, string $defaultFolderId, string $entity): void
    {
        $connection->transactional(function (Connection $connection) use ($defaultFolderId, $entity): void {
            $configurationId = Uuid::randomBytes();
            $folderId = Uuid::randomBytes();
            $folderName = $this->getMediaFolderName($entity);
            $private = 0;
            if ($entity === 'document') {
                $private = 1;
            }
            $connection->executeStatement('
                INSERT INTO `media_folder_configuration` (`id`, `thumbnail_quality`, `create_thumbnails`, `private`, created_at)
                VALUES (:id, 80, 1, :private, :createdAt)
            ', [
                'id' => $configurationId,
                'createdAt' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'private' => $private,
            ]);

            $connection->executeStatement('
                INSERT into `media_folder` (`id`, `name`, `default_folder_id`, `media_folder_configuration_id`, `use_parent_configuration`, `child_count`, `created_at`)
                VALUES (:folderId, :folderName, :defaultFolderId, :configurationId, 0, 0, :createdAt)
            ', [
                'folderId' => $folderId,
                'folderName' => $folderName,
                'defaultFolderId' => $defaultFolderId,
                'configurationId' => $configurationId,
                'createdAt' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ]);
        });
    }

    private function getMediaFolderName(string $entity): string
    {
        $capitalizedEntityParts = array_map(
            static fn ($part) => ucfirst((string) $part),
            explode('_', $entity)
        );

        return implode(' ', $capitalizedEntityParts) . ' Media';
    }

    private function createSystemConfig(Connection $connection): void
    {

        $connection->insert('system_config', [
            'id' => Uuid::randomBytes(),
            'configuration_key' => 'core.store.apiUri',
            'configuration_value' => '{"_value": "https://api.58shuwei.com"}',
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);

        $connection->insert('system_config', [
            'id' => Uuid::randomBytes(),
            'configuration_key' => 'core.register.minPasswordLength',
            'configuration_value' => '{"_value": 8}',
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


    private function createDefaultSnippetSets(Connection $connection): void
    {
        $queue = new MultiInsertQueryQueue($connection);

        $queue->addInsert('snippet_set', ['id' => Uuid::randomBytes(), 'name' => 'BASE zh-CN', 'base_file' => 'messages.zh-CN', 'iso' => 'zh-CN', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $queue->addInsert('snippet_set', ['id' => Uuid::randomBytes(), 'name' => 'BASE en-US', 'base_file' => 'messages.en-US', 'iso' => 'en-GB', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);

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
//        $connection->insert('language', [
//            'id' => $languageEn,
//            'name' => 'English',
//            'locale_id' => $localeEn,
//            'translation_code_id' => $localeEn,
//            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
//        ]);

        $connection->insert('language', [
            'id' => $languageZh,
            'name' => '中文',
            'locale_id' => $localeZh,
            'translation_code_id' => $localeZh,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);

        $connection->insert('locale_translation', [
            'locale_id' => $localeZh,
            'language_id' => $languageZh,
            'name' => '中文',
            'territory' => '中国',
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
