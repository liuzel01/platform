<?php declare(strict_types=1);

namespace Shuwei\Core\System\Snippet;

use Shuwei\Core\Framework\Log\Package;

#[Package('system-settings')]
class SnippetEvents
{
    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const SNIPPET_WRITTEN_EVENT = 'snippet.written';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const SNIPPET_DELETED_EVENT = 'snippet.deleted';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const SNIPPET_LOADED_EVENT = 'snippet.loaded';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const SNIPPET_SEARCH_RESULT_LOADED_EVENT = 'snippet.search.result.loaded';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const SNIPPET_AGGREGATION_LOADED_EVENT = 'snippet.aggregation.result.loaded';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const SNIPPET_ID_SEARCH_RESULT_LOADED_EVENT = 'snippet.id.search.result.loaded';

    /* SnippetSet */
    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const SNIPPET_SET_WRITTEN_EVENT = 'snippet_set.written';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const SNIPPET_SET_DELETED_EVENT = 'snippet_set.deleted';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const SNIPPET_SET_LOADED_EVENT = 'snippet_set.loaded';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const SNIPPET_SET_SEARCH_RESULT_LOADED_EVENT = 'snippet_set.search.result.loaded';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const SNIPPET_SET_AGGREGATION_LOADED_EVENT = 'snippet_set.aggregation.result.loaded';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const SNIPPET_SET_ID_SEARCH_RESULT_LOADED_EVENT = 'snippet_set.id.search.result.loaded';
}
