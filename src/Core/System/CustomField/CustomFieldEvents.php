<?php declare(strict_types=1);

namespace Shuwei\Core\System\CustomField;

use Shuwei\Core\Framework\Log\Package;

#[Package('system-settings')]
class CustomFieldEvents
{
    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const CUSTOM_FIELD_WRITTEN_EVENT = 'custom_field.written';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const CUSTOM_FIELD_DELETED_EVENT = 'custom_field.deleted';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const CUSTOM_FIELD_LOADED_EVENT = 'custom_field.loaded';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const CUSTOM_FIELD_SEARCH_RESULT_LOADED_EVENT = 'custom_field.search.result.loaded';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const CUSTOM_FIELD_AGGREGATION_LOADED_EVENT = 'custom_field.aggregation.result.loaded';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const CUSTOM_FIELD_ID_SEARCH_RESULT_LOADED_EVENT = 'custom_field.id.search.result.loaded';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const CUSTOM_FIELD_SET_WRITTEN_EVENT = 'custom_field_set.written';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const CUSTOM_FIELD_SET_DELETED_EVENT = 'custom_field_set.deleted';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const CUSTOM_FIELD_SET_LOADED_EVENT = 'custom_field_set.loaded';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const CUSTOM_FIELD_SET_SEARCH_RESULT_LOADED_EVENT = 'custom_field_set.search.result.loaded';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const CUSTOM_FIELD_SET_AGGREGATION_LOADED_EVENT = 'custom_field_set.aggregation.result.loaded';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const CUSTOM_FIELD_SET_ID_SEARCH_RESULT_LOADED_EVENT = 'custom_field_set.id.search.result.loaded';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const CUSTOM_FIELD_SET_RELATION_WRITTEN_EVENT = 'custom_field_set_relation.written';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const CUSTOM_FIELD_SET_RELATION_DELETED_EVENT = 'custom_field_set_relation.deleted';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const CUSTOM_FIELD_SET_RELATION_LOADED_EVENT = 'custom_field_set_relation.loaded';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const CUSTOM_FIELD_SET_RELATION_SEARCH_RESULT_LOADED_EVENT = 'custom_field_set_relation.search.result.loaded';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const CUSTOM_FIELD_SET_RELATION_AGGREGATION_LOADED_EVENT = 'custom_field_set_relation.aggregation.result.loaded';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const CUSTOM_FIELD_SET_RELATION_ID_SEARCH_RESULT_LOADED_EVENT = 'custom_field_set_relation.id.search.result.loaded';
}
