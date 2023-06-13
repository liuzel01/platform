<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer;

use Shuwei\Core\Framework\DataAbstractionLayer\Dbal\EntityDefinitionQueryHelper;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\AssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\BlobField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\CalculatedPriceField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\CartPriceField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\ChildCountField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\ChildrenAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\DateField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Field;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\RestrictDelete;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Runtime;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\FloatField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\ListField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\ParentAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\ParentFkField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\PasswordField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\PriceDefinitionField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\PriceField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\RemoteAddressField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\StorageAware;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\TreeLevelField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\TreePathField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\VersionField;
use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
class SchemaGenerator
{
    private string $tableTemplate = <<<EOL
CREATE TABLE `#name#` (
    #columns#,
    #keys#
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOL;

    private string $columnTemplate = <<<EOL
    `#name#` #type# #nullable# #default#
EOL;

    public function generate(EntityDefinition $definition)
    {
        $table = $definition->getEntityName();

        $columns = [];

        foreach ($definition->getFields() as $field) {
            $columns[] = $this->generateFieldColumn($field);
        }
        $columns = array_filter($columns);

        $primaryKey = $this->generatePrimaryKey($definition);

        $foreignKeys = $this->generateForeignKeys($definition);

        $jsonValidations = $this->generateJsonChecks($definition);

        $keys = array_filter([$primaryKey, $jsonValidations, $foreignKeys]);
        $keys = implode(",\n", $keys);

        $template = str_replace(
            ['#name#', '#columns#', '#keys#'],
            [$table, implode(",\n    ", $columns), $keys],
            $this->tableTemplate
        );

        return $template;
    }

    private function generateFieldColumn(Field $field): ?string
    {
        if ($field->is(Runtime::class)) {
            return null;
        }
        if ($field instanceof AssociationField) {
            return null;
        }
        if (!$field instanceof StorageAware) {
            return null;
        }

        $default = '';

        $nullable = 'NULL';
        if ($field->is(Required::class) && !$field instanceof UpdatedAtField) {
            $nullable = 'NOT NULL';
        }

        switch (true) {
            case $field instanceof VersionField:
            case $field instanceof ReferenceVersionField:
            case $field instanceof ParentFkField:
            case $field instanceof IdField:
            case $field instanceof FkField:
                $type = 'BINARY(16)';

                break;

            case $field instanceof DateTimeField:
                $type = 'DATETIME(3)';

                break;

            case $field instanceof DateField:
                $type = 'DATE';

                break;

            case $field instanceof TranslatedField:
                return null;

            case $field instanceof CartPriceField:
            case $field instanceof CalculatedPriceField:
            case $field instanceof PriceDefinitionField:
            case $field instanceof PriceField:
            case $field instanceof ListField:
            case $field instanceof JsonField:
                $type = 'JSON';

                break;

            case $field instanceof TreePathField:
            case $field instanceof LongTextField:
                $type = 'LONGTEXT';

                break;

            case $field instanceof TreeLevelField:
                $type = 'INT';

                break;

            case $field instanceof ChildCountField:
            case $field instanceof IntField:
                $type = 'INT(11)';

                break;

            case $field instanceof RemoteAddressField:
                $type = 'VARCHAR(255)';

                break;

            case $field instanceof PasswordField:
                $type = 'VARCHAR(1024)';

                break;

            case $field instanceof FloatField:
                $type = 'DOUBLE';

                break;

            case $field instanceof StringField:
                $type = 'VARCHAR(' . $field->getMaxLength() . ')';

                break;

            case $field instanceof BoolField:
                $type = 'TINYINT(1)';
                $default = 'DEFAULT \'0\'';

                break;

            case $field instanceof BlobField:
                $type = 'LONGBLOB';

                break;

            default:
                throw new \RuntimeException(sprintf('Unknown field %s', $field::class));
        }

        $template = str_replace(
            ['#name#', '#type#', '#nullable#', '#default#'],
            [$field->getStorageName(), $type, $nullable, $default],
            $this->columnTemplate
        );

        return trim($template);
    }

    private function generatePrimaryKey(EntityDefinition $definition): string
    {
        $keys = [];

        /** @var StorageAware $primaryKey */
        foreach ($definition->getPrimaryKeys() as $primaryKey) {
            $keys[] = sprintf('`%s`', $primaryKey->getStorageName());
        }

        if (empty($keys)) {
            throw new \RuntimeException(sprintf('No primary key detected for entity: %s', $definition->getEntityName()));
        }

        return 'PRIMARY KEY (' . implode(',', $keys) . ')';
    }

    private function generateForeignKeys(EntityDefinition $definition): string
    {
        $fields = $definition->getFields()->filter(
            function (Field $field) {
                if (!$field instanceof ManyToOneAssociationField) {
                    return false;
                }

                return true;
            }
        );

        $referenceVersionFields = $definition->getFields()->filterInstance(ReferenceVersionField::class);

        $indices = [];
        $constraints = [];

        /** @var ManyToOneAssociationField $field */
        foreach ($fields as $field) {
            $reference = $field->getReferenceDefinition();

            $hasOneToMany = $definition->getFields()->filter(function (Field $field) use ($reference) {
                if (!$field instanceof OneToManyAssociationField) {
                    return false;
                }
                if ($field instanceof ChildrenAssociationField) {
                    return false;
                }

                return $field->getReferenceDefinition() === $reference;
            })->count() > 0;

            $columns = [
                EntityDefinitionQueryHelper::escape($field->getStorageName()),
            ];

            $referenceColumns = [
                EntityDefinitionQueryHelper::escape($field->getReferenceField()),
            ];

            if ($reference->isVersionAware()) {
                $versionField = null;

                /** @var ReferenceVersionField $referenceVersionField */
                foreach ($referenceVersionFields as $referenceVersionField) {
                    if ($referenceVersionField->getVersionReferenceDefinition() === $reference) {
                        $versionField = $referenceVersionField;

                        break;
                    }
                }

                if ($field instanceof ParentAssociationField) {
                    $columns[] = '`version_id`';
                } else {
                    $columns[] = EntityDefinitionQueryHelper::escape($versionField->getStorageName());
                }

                $referenceColumns[] = '`version_id`';
            }

            $update = 'CASCADE';

            $parameters = [
                '#entity#' => $definition->getEntityName(),
                '#column#' => $field->getStorageName(),
                '#columns#' => implode(',', $columns),
            ];
            $indices[] = str_replace(
                array_keys($parameters),
                array_values($parameters),
                '    KEY `fk.#entity#.#column#` (#columns#)'
            );

            if ($field->is(CascadeDelete::class)) {
                $delete = 'CASCADE';
            } elseif ($field->is(RestrictDelete::class)) {
                $delete = 'RESTRICT';
            } else {
                $delete = 'SET NULL';
            }

            // skip foreign key to prevent bi-directional foreign key
            if ($hasOneToMany) {
                continue;
            }

            $parameters = [
                '#entity#' => $definition->getEntityName(),
                '#column#' => $field->getStorageName(),
                '#columns#' => implode(',', $columns),
                '#refEntity#' => $reference->getEntityName(),
                '#refColumns#' => implode(',', $referenceColumns),
                '#delete#' => $delete,
                '#update#' => $update,
            ];

            $constraints[] = str_replace(
                array_keys($parameters),
                array_values($parameters),
                '    CONSTRAINT `fk.#entity#.#column#` FOREIGN KEY (#columns#) REFERENCES `#refEntity#` (#refColumns#) ON DELETE #delete# ON UPDATE #update#'
            );
        }

        $constraints = implode(",\n", array_filter($constraints));
        $indices = implode(",\n", array_filter($indices));

        return implode(",\n", array_filter([$indices, $constraints]));
    }

    private function generateJsonChecks(EntityDefinition $definition): string
    {
        $fields = $definition->getFields()->filterInstance(JsonField::class);

        $template = '    CONSTRAINT `json.#entity#.#column#` CHECK (JSON_VALID(`#column#`))';
        $checks = [];

        /** @var JsonField $field */
        foreach ($fields as $field) {
            $parameters = [
                '#entity#' => $definition->getEntityName(),
                '#column#' => $field->getStorageName(),
            ];

            $checks[] = str_replace(array_keys($parameters), array_values($parameters), $template);
        }

        return implode(",\n", $checks);
    }
}
