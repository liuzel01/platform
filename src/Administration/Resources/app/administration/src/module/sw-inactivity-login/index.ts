import './page/index';

import type { Route } from 'vue-router';
import enUS from './snippet/en-US.json';
import zhCN from './snippet/zh-CN.json';

const { Module } = Shuwei;

/**
 * @package admin
 *
 * @private
 */
Module.register('sw-inactivity-login', {
    type: 'core',
    name: 'inactivity-login',
    title: 'sw-inactivity-login.general.mainMenuItemIndex',
    description: 'sw-inactivity-login.general.description',
    version: '1.0.0',
    targetVersion: '1.0.0',
    color: '#F19D12',

    snippets: {
        'en-US': enUS,
        'zh-CN': zhCN,
    },

    routes: {
        index: {
            component: 'sw-inactivity-login',
            path: '/inactivity/login/:id',
            coreRoute: true,
            props: {
                default(route: Route) {
                    return {
                        hash: route.params.id,
                    };
                },
            },
        },
    },
});
