import './sw-shuwei-updates-info.scss';
import template from './sw-shuwei-updates-info.html.twig';

const { Component } = Shuwei;

/**
 * @package system-settings
 * @deprecated tag:v6.6.0 - Will be private
 */
// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
Component.register('sw-settings-shuwei-updates-info', {
    template,

    props: {
        changelog: {
            type: String,
            required: true,
        },
        isLoading: {
            type: Boolean,
        },
    },
});
