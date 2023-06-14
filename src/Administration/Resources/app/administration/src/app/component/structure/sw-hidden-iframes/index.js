import { MAIN_HIDDEN } from '@58shuwei-ag/admin-extension-sdk/es/location';
import template from './sw-hidden-iframes.html.twig';

const { Component } = Shuwei;

/**
 * @package admin
 *
 * @private
 */
Component.register('sw-hidden-iframes', {
    template,

    computed: {
        extensions() {
            return Shuwei.State.getters['extensions/privilegedExtensions'];
        },

        MAIN_HIDDEN() {
            return MAIN_HIDDEN;
        },
    },
});
