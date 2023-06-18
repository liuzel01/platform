/**
 * @package admin
 */

/* @private */
export {};

type SalutationFilterEntityType = {
    salutation: {
        id: string,
        salutationKey: string,
        displayName: string
    },
    title: string,
    name: string,
    [key: string]: unknown
};

/**
 * @deprecated tag:v6.6.0 - Will be private
 */
Shuwei.Mixin.register('salutation', {
    computed: {
        salutationFilter(): (entity: SalutationFilterEntityType, fallbackSnippet: string) => string {
            return Shuwei.Filter.getByName('salutation');
        },
    },

    methods: {
        salutation(entity: SalutationFilterEntityType, fallbackSnippet = '') {
            return this.salutationFilter(entity, fallbackSnippet);
        },
    },
});
