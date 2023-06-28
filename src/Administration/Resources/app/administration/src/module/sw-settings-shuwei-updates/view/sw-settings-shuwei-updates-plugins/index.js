import template from './sw-shuwei-updates-plugins.html.twig';

const { Component } = Shuwei;

/**
 * @package system-settings
 * @deprecated tag:v6.6.0 - Will be private
 */
// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
Component.register('sw-settings-shuwei-updates-plugins', {
    template,

    inject: ['feature'],

    props: {
        isLoading: {
            type: Boolean,
        },
        plugins: {
            type: Array,
            default: () => [],
        },
    },
    computed: {
        columns() {
            return [
                {
                    property: 'name',
                    label: this.$tc('sw-settings-shuwei-updates.plugins.columns.name'),
                    rawData: true,
                },
                {
                    property: 'icon',
                    label: this.$tc('sw-settings-shuwei-updates.plugins.columns.available'),
                    rawData: true,
                },
            ];
        },
    },

    methods: {
        openMyExtensions() {
            this.$router.push({ name: 'sw.extension.my-extensions.listing.app' });
        },
    },
});
