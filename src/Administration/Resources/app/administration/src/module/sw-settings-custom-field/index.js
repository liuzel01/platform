/**
 * @package system-settings
 */
import './acl';

const { Module } = Shuwei;

/* eslint-disable max-len, sw-deprecation-rules/private-feature-declarations */
Shuwei.Component.extend('sw-settings-custom-field-set-create', 'sw-settings-custom-field-set-detail', () => import('./page/sw-settings-custom-field-set-create'));
Shuwei.Component.register('sw-settings-custom-field-set-list', () => import('./page/sw-settings-custom-field-set-list'));
Shuwei.Component.register('sw-settings-custom-field-set-detail', () => import('./page/sw-settings-custom-field-set-detail'));
Shuwei.Component.register('sw-custom-field-translated-labels', () => import('./component/sw-custom-field-translated-labels'));
Shuwei.Component.register('sw-custom-field-set-detail-base', () => import('./component/sw-custom-field-set-detail-base'));
Shuwei.Component.register('sw-custom-field-list', () => import('./component/sw-custom-field-list'));
Shuwei.Component.register('sw-custom-field-detail', () => import('./component/sw-custom-field-detail'));
Shuwei.Component.register('sw-custom-field-type-base', () => import('./component/sw-custom-field-type-base'));
Shuwei.Component.extend('sw-custom-field-type-select', 'sw-custom-field-type-base', () => import('./component/sw-custom-field-type-select'));
Shuwei.Component.extend('sw-custom-field-type-entity', 'sw-custom-field-type-select', () => import('./component/sw-custom-field-type-entity'));
Shuwei.Component.extend('sw-custom-field-type-text', 'sw-custom-field-type-base', () => import('./component/sw-custom-field-type-text'));
Shuwei.Component.extend('sw-custom-field-type-number', 'sw-custom-field-type-base', () => import('./component/sw-custom-field-type-number'));
Shuwei.Component.extend('sw-custom-field-type-date', 'sw-custom-field-type-base', () => import('./component/sw-custom-field-type-date'));
Shuwei.Component.extend('sw-custom-field-type-checkbox', 'sw-custom-field-type-base', () => import('./component/sw-custom-field-type-checkbox'));
Shuwei.Component.extend('sw-custom-field-type-text-editor', 'sw-custom-field-type-base', () => import('./component/sw-custom-field-type-text-editor'));
/* eslint-enable max-len, sw-deprecation-rules/private-feature-declarations */

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
Module.register('sw-settings-custom-field', {
    type: 'core',
    name: 'settings-custom-field',
    title: 'sw-settings-custom-field.general.mainMenuItemGeneral',
    description: 'sw-settings-custom-field.general.description',
    color: '#9AA8B5',
    icon: 'regular-cog',
    favicon: 'icon-module-settings.png',
    entity: 'custom-field-set',

    routes: {
        index: {
            component: 'sw-settings-custom-field-set-list',
            path: 'index',
            meta: {
                parentPath: 'sw.settings.index.system',
                privilege: 'custom_field.viewer',
            },
        },
        detail: {
            component: 'sw-settings-custom-field-set-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'sw.settings.custom.field.index',
                privilege: 'custom_field.viewer',
            },
        },
        create: {
            component: 'sw-settings-custom-field-set-create',
            path: 'create',
            meta: {
                parentPath: 'sw.settings.custom.field.index',
                privilege: 'custom_field.creator',
            },
        },
    },

    settingsItem: {
        group: 'system',
        to: 'sw.settings.custom.field.index',
        icon: 'regular-bars-square',
        privilege: 'custom_field.viewer',
    },
});
