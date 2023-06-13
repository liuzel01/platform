<?php declare(strict_types=1);

namespace Shuwei\Core\System\Language;

use Shuwei\Core\Framework\Log\Package;

#[Package('system-settings')]
class LanguageEvents
{
    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const LANGUAGE_WRITTEN_EVENT = 'language.written';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const LANGUAGE_DELETED_EVENT = 'language.deleted';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const LANGUAGE_LOADED_EVENT = 'language.loaded';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const LANGUAGE_SEARCH_RESULT_LOADED_EVENT = 'language.search.result.loaded';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const LANGUAGE_AGGREGATION_LOADED_EVENT = 'language.aggregation.result.loaded';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const LANGUAGE_ID_SEARCH_RESULT_LOADED_EVENT = 'language.id.search.result.loaded';
}
