import template from './sw-shuwei-updates-requirements.html.twig';

const { Component } = Shuwei;

/**
 * @package system-settings
 * @deprecated tag:v6.6.0 - Will be private
 */
// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
Component.register('sw-settings-shuwei-updates-requirements', {
    template,

    props: {
        updateInfo: {
            type: Object,
            required: true,
            default: () => {},
        },
        requirements: {
            type: Array,
            required: true,
            default: () => [],
        },
        isLoading: {
            type: Boolean,
        },
    },

    data() {
        return {
            columns: [
                {
                    property: 'message',
                    label: this.$t('sw-settings-shuwei-updates.requirements.columns.message'),
                    rawData: true,
                },
                {
                    property: 'result',
                    label: this.$t('sw-settings-shuwei-updates.requirements.columns.status'),
                    rawData: true,
                },
            ],
        };
    },
});
