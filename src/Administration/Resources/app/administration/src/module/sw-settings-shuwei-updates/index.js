import './page/sw-settings-shuwei-updates-wizard';
import './page/sw-settings-shuwei-updates-index';
import './view/sw-settings-shuwei-updates-info';
import './view/sw-settings-shuwei-updates-requirements';
import './view/sw-settings-shuwei-updates-plugins';
import './acl';

const { Module } = Shuwei;

/**
 * @deprecated tag:v6.6.0 - Will be private
 */
// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
Module.register('sw-settings-shuwei-updates', {
    type: 'core',
    name: 'settings-shuwei-updates',
    title: 'sw-settings-shuwei-updates.general.emptyTitle',
    description: 'sw-settings-shuwei-updates.general.emptyTitle',
    version: '1.0.0',
    targetVersion: '1.0.0',
    color: '#9AA8B5',
    icon: 'regular-cog',
    favicon: 'icon-module-settings.png',

    routes: {
        wizard: {
            component: 'sw-settings-shuwei-updates-wizard',
            path: 'wizard',
            meta: {
                parentPath: 'sw.settings.index.system',
                privilege: 'system.core_update',
            },
        },
    },

    settingsItem: {
        privilege: 'system.core_update',
        group: 'system',
        to: 'sw.settings.shuwei.updates.wizard',
        icon: 'regular-sync',
    },
});
