/**
 * @package system-settings
 */
import template from './sw-admin-menu.html.twig';

const { Component } = Shuwei;

Component.override('sw-admin-menu', {
    template,
    inject: ['acl'],

});
