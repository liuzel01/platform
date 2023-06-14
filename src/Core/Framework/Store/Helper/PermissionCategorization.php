<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Helper;

use Shuwei\Core\Framework\DataAbstractionLayer\Version\VersionDefinition;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\CustomField\Aggregate\CustomFieldSet\CustomFieldSetDefinition;
use Shuwei\Core\System\CustomField\Aggregate\CustomFieldSetRelation\CustomFieldSetRelationDefinition;
use Shuwei\Core\System\CustomField\CustomFieldDefinition;
use Shuwei\Core\System\Language\LanguageDefinition;
use Shuwei\Core\System\Locale\Aggregate\LocaleTranslation\LocaleTranslationDefinition;
use Shuwei\Core\System\Locale\LocaleDefinition;
use Shuwei\Core\System\User\Aggregate\UserRecovery\UserRecoveryDefinition;
use Shuwei\Core\System\User\UserDefinition;

/**
 * @internal
 */
#[Package('merchant-services')]
class PermissionCategorization
{

    private const CATEGORY_OTHER = 'other';

    private const PERMISSION_CATEGORIES = [

    ];

    public static function isInCategory(string $entity, string $category): bool
    {
        if ($category === self::CATEGORY_OTHER) {
            $allCategories = array_merge(...array_values(self::PERMISSION_CATEGORIES));

            return !\in_array($entity, $allCategories, true);
        }

        return \in_array($entity, self::PERMISSION_CATEGORIES[$category], true);
    }

    /**
     * @return string[]
     */
    public static function getCategoryNames(): array
    {
        $categories = array_keys(self::PERMISSION_CATEGORIES);
        $categories[] = self::CATEGORY_OTHER;

        return $categories;
    }
}
