import './sw-wizard-dot-navigation.scss';
import template from './sw-wizard-dot-navigation.html.twig';

const { Component } = Shuwei;

/**
 * See `sw-wizard` for an example.
 *
 * @private
 */
Component.register('sw-wizard-dot-navigation', {
    template,

    props: {
        pages: {
            type: Array,
            required: true,
        },
        activePage: {
            type: Number,
            required: true,
        },
    },
});
