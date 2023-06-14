/**
 * @package system-settings
 */
import './acl';

/* eslint-disable max-len, sw-deprecation-rules/private-feature-declarations */
Shuwei.Component.register('sw-users-permissions', () => import('./page/sw-users-permissions'));
Shuwei.Component.register('sw-users-permissions-user-listing', () => import('./components/sw-users-permissions-user-listing'));
Shuwei.Component.register('sw-users-permissions-role-listing', () => import('./components/sw-users-permissions-role-listing'));
Shuwei.Component.register('sw-users-permissions-configuration', () => import('./components/sw-users-permissions-configuration'));
Shuwei.Component.register('sw-users-permissions-additional-permissions', () => import('./components/sw-users-permissions-additional-permissions'));
Shuwei.Component.register('sw-users-permissions-permissions-grid', () => import('./components/sw-users-permissions-permissions-grid'));
Shuwei.Component.register('sw-users-permissions-detailed-permissions-grid', () => import('./components/sw-users-permissions-detailed-permissions-grid'));
Shuwei.Component.register('sw-users-permissions-detailed-additional-permissions', () => import('./components/sw-users-permissions-detailed-additional-permissions'));
Shuwei.Component.register('sw-users-permissions-user-detail', () => import('./page/sw-users-permissions-user-detail'));
Shuwei.Component.extend('sw-users-permissions-user-create', 'sw-users-permissions-user-detail', () => import('./page/sw-users-permissions-user-create'));
Shuwei.Component.register('sw-users-permissions-role-detail', () => import('./page/sw-users-permissions-role-detail'));
Shuwei.Component.register('sw-users-permissions-role-view-general', () => import('./view/sw-users-permissions-role-view-general'));
Shuwei.Component.register('sw-users-permissions-role-view-detailed', () => import('./view/sw-users-permissions-role-view-detailed'));
/* eslint-enable max-len, sw-deprecation-rules/private-feature-declarations */

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
Shuwei.Module.register('sw-users-permissions', {
    type: 'core',
    name: 'users-permissions',
    title: 'sw-users-permissions.general.label',
    description: 'sw-users-permissions.general.label',
    version: '1.0.0',
    targetVersion: '1.0.0',
    color: '#9AA8B5',
    icon: 'regular-cog',
    favicon: 'icon-module-settings.png',
    entity: 'user',

    routes: {
        index: {
            component: 'sw-users-permissions',
            path: 'index',
            meta: {
                parentPath: 'sw.settings.index.system',
                privilege: 'users_and_permissions.viewer',
            },
        },
        'user.detail': {
            component: 'sw-users-permissions-user-detail',
            path: 'user.detail/:id?',
            meta: {
                parentPath: 'sw.users.permissions.index',
                privilege: 'users_and_permissions.viewer',
            },
        },
        'user.create': {
            component: 'sw-users-permissions-user-create',
            path: 'user.create',
            meta: {
                parentPath: 'sw.users.permissions.index',
                privilege: 'users_and_permissions.creator',
            },
        },
        'role.detail': {
            component: 'sw-users-permissions-role-detail',
            path: 'role.detail/:id?',
            meta: {
                parentPath: 'sw.users.permissions.index',
                privilege: 'users_and_permissions.viewer',
            },
            redirect: {
                name: 'sw.users.permissions.role.detail.general',
            },
            children: {
                general: {
                    component: 'sw-users-permissions-role-view-general',
                    path: 'general',
                    meta: {
                        parentPath: 'sw.users.permissions.index',
                        privilege: 'users_and_permissions.viewer',
                    },
                },
                'detailed-privileges': {
                    component: 'sw-users-permissions-role-view-detailed',
                    path: 'detailed-privileges',
                    meta: {
                        parentPath: 'sw.users.permissions.index',
                        privilege: 'users_and_permissions.viewer',
                    },
                },
            },
        },
    },

    settingsItem: {
        group: 'system',
        to: 'sw.users.permissions.index',
        icon: 'regular-user',
        privilege: 'users_and_permissions.viewer',
    },
});
