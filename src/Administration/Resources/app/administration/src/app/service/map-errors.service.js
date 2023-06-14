/**
 * @package admin
 */

const { string } = Shuwei.Utils;

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export function mapPropertyErrors(entityName, properties = []) {
    const computedValues = {};

    properties.forEach((property) => {
        const computedValueName = string.camelCase(`${entityName}.${property}.error`);

        computedValues[computedValueName] = function getterPropertyError() {
            const entity = this[entityName];

            const isEntity = entity && typeof entity.getEntityName === 'function';
            if (!isEntity) {
                return null;
            }

            return Shuwei.State.getters['error/getApiError'](entity, property);
        };
    });

    return computedValues;
}

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export function mapSystemConfigErrors(entityName, saleChannelId, key = '') {
    return Shuwei.State.getters['error/getSystemConfigApiError'](entityName, saleChannelId, key);
}

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export function mapCollectionPropertyErrors(entityCollectionName, properties = []) {
    const computedValues = {};

    properties.forEach((property) => {
        const computedValueName = string.camelCase(`${entityCollectionName}.${property}.error`);

        computedValues[computedValueName] = function getterCollectionError() {
            const entityCollection = this[entityCollectionName];

            if (!Array.isArray(entityCollection)) {
                return null;
            }

            return entityCollection.map((entity) => Shuwei.State.getters['error/getApiError'](entity, property));
        };
    });

    return computedValues;
}

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export function mapPageErrors(errorConfig) {
    const map = {};
    Object.keys(errorConfig).forEach((routeName) => {
        const subjects = errorConfig[routeName];
        map[`${string.camelCase(routeName)}Error`] = function getterPropertyError() {
            return Object.keys(subjects).some((entityName) => {
                return Shuwei.State.getters['error/existsErrorInProperty'](entityName, subjects[entityName]);
            });
        };
    });
    return map;
}
