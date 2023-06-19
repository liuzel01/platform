/**
 * @module app/service/search-type
 */

/**
 *
 * @memberOf module:core/service/search-type
 * @constructor
 * @method createSearchTypeService
 * @returns {Object}
 */
// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default function createSearchTypeService() {
    const typeStore = {
        property_group: {
            entityName: 'property_group',
            placeholderSnippet: 'sw-property.general.placeholderSearchBar',
            listingRoute: 'sw.property.index',
            hideOnGlobalSearchBar: true,
        },
        sales_channel: {
            entityName: 'sales_channel',
            placeholderSnippet: 'sw-sales-channel.general.placeholderSearchBar',
            listingRoute: 'sw.sales.channel.index',
            hideOnGlobalSearchBar: true,
        },
    };

    let $typeStore = {};

    $typeStore = {
        all: {
            entityName: '',
            placeholderSnippet: '',
            listingRoute: '',
        },
        ...typeStore,
    };

    return {
        getTypeByName,
        upsertType,
        getTypes,
        removeType,
    };

    function getTypeByName(type) {
        return $typeStore[type];
    }

    function upsertType(name, configuration) {
        $typeStore[name] = { ...$typeStore[name], ...configuration };
    }

    function getTypes() {
        return $typeStore;
    }

    function removeType(name) {
        delete $typeStore[name];
    }
}
