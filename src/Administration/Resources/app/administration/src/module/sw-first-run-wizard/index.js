const { Module } = Shuwei;

/* eslint-disable max-len, sw-deprecation-rules/private-feature-declarations */
Shuwei.Component.register('sw-first-run-wizard-modal', () => import('./component/sw-first-run-wizard-modal'));
Shuwei.Component.register('sw-plugin-card', () => import('./component/sw-plugin-card'));
Shuwei.Component.register('sw-first-run-wizard', () => import('./page/index'));
Shuwei.Component.register('sw-first-run-wizard-welcome', () => import('./view/sw-first-run-wizard-welcome'));
Shuwei.Component.register('sw-first-run-wizard-data-import', () => import('./view/sw-first-run-wizard-data-import'));
Shuwei.Component.register('sw-first-run-wizard-mailer-base', () => import('./view/sw-first-run-wizard-mailer-base'));
Shuwei.Component.register('sw-first-run-wizard-mailer-selection', () => import('./view/sw-first-run-wizard-mailer-selection'));
Shuwei.Component.register('sw-first-run-wizard-mailer-smtp', () => import('./view/sw-first-run-wizard-mailer-smtp'));
Shuwei.Component.register('sw-first-run-wizard-mailer-local', () => import('./view/sw-first-run-wizard-mailer-local'));
Shuwei.Component.register('sw-first-run-wizard-paypal-base', () => import('./view/sw-first-run-wizard-paypal-base'));
Shuwei.Component.register('sw-first-run-wizard-paypal-info', () => import('./view/sw-first-run-wizard-paypal-info'));
Shuwei.Component.register('sw-first-run-wizard-paypal-credentials', () => import('./view/sw-first-run-wizard-paypal-credentials'));
Shuwei.Component.register('sw-first-run-wizard-plugins', () => import('./view/sw-first-run-wizard-plugins'));
Shuwei.Component.register('sw-first-run-wizard-shuwei-base', () => import('./view/sw-first-run-wizard-shuwei-base'));
Shuwei.Component.register('sw-first-run-wizard-shuwei-account', () => import('./view/sw-first-run-wizard-shuwei-account'));
Shuwei.Component.register('sw-first-run-wizard-shuwei-domain', () => import('./view/sw-first-run-wizard-shuwei-domain'));
Shuwei.Component.register('sw-first-run-wizard-defaults', () => import('./view/sw-first-run-wizard-defaults'));
Shuwei.Component.register('sw-first-run-wizard-store', () => import('./view/sw-first-run-wizard-store'));
Shuwei.Component.register('sw-first-run-wizard-finish', () => import('./view/sw-first-run-wizard-finish'));
/* eslint-enable max-len, sw-deprecation-rules/private-feature-declarations */

/**
 * @package merchant-services
 * @deprecated tag:v6.6.0 - Will be private
 */
// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
Module.register('sw-first-run-wizard', {
    type: 'core',
    name: 'first-run-wizard',
    title: 'sw-first-run-wizard.general.mainMenuItemGeneral',
    description: 'First Run Wizard to set up languages and plugins after the installation process',
    version: '1.0.0',
    targetVersion: '1.0.0',
    color: '#F19D12',

    routes: {
        index: {
            component: 'sw-first-run-wizard',
            path: 'index',
            meta: {
                privilege: 'admin',
            },
            redirect: {
                name: 'sw.first.run.wizard.index.welcome',
            },
            children: {
                welcome: {
                    component: 'sw-first-run-wizard-welcome',
                    path: '',
                    meta: {
                        privilege: 'admin',
                    },
                },
                'data-import': {
                    component: 'sw-first-run-wizard-data-import',
                    path: 'data-import',
                    meta: {
                        privilege: 'admin',
                    },
                },
                mailer: {
                    component: 'sw-first-run-wizard-mailer-base',
                    path: 'mailer',
                    meta: {
                        privilege: 'admin',
                    },
                    children: {
                        selection: {
                            component: 'sw-first-run-wizard-mailer-selection',
                            path: 'selection',
                            meta: {
                                privilege: 'admin',
                            },
                        },
                        smtp: {
                            component: 'sw-first-run-wizard-mailer-smtp',
                            path: 'smtp',
                            meta: {
                                privilege: 'admin',
                            },
                        },
                        local: {
                            component: 'sw-first-run-wizard-mailer-local',
                            path: 'local',
                            meta: {
                                privilege: 'admin',
                            },
                        },
                    },
                },
                paypal: {
                    component: 'sw-first-run-wizard-paypal-base',
                    path: 'paypal',
                    meta: {
                        privilege: 'admin',
                    },
                    children: {
                        info: {
                            component: 'sw-first-run-wizard-paypal-info',
                            path: 'info',
                            meta: {
                                privilege: 'admin',
                            },
                        },
                        install: {
                            component: 'sw-first-run-wizard-paypal-install',
                            path: 'install',
                            meta: {
                                privilege: 'admin',
                            },
                        },
                        credentials: {
                            component: 'sw-first-run-wizard-paypal-credentials',
                            path: 'credentials',
                            meta: {
                                privilege: 'admin',
                            },
                        },
                    },
                },
                plugins: {
                    component: 'sw-first-run-wizard-plugins',
                    path: 'plugins',
                    meta: {
                        privilege: 'admin',
                    },
                },
                shuwei: {
                    component: 'sw-first-run-wizard-shuwei-base',
                    path: 'shuwei',
                    children: {
                        account: {
                            component: 'sw-first-run-wizard-shuwei-account',
                            path: 'account',
                            meta: {
                                privilege: 'admin',
                            },
                        },
                        domain: {
                            component: 'sw-first-run-wizard-shuwei-domain',
                            path: 'domain',
                            meta: {
                                privilege: 'admin',
                            },
                        },
                    },
                },
                store: {
                    component: 'sw-first-run-wizard-store',
                    path: 'store',
                    meta: {
                        privilege: 'admin',
                    },
                },
                defaults: {
                    component: 'sw-first-run-wizard-defaults',
                    path: 'defaults',
                    meta: {
                        privilege: 'admin',
                    },
                },
                finish: {
                    component: 'sw-first-run-wizard-finish',
                    path: 'finish',
                    meta: {
                        privilege: 'admin',
                    },
                },
            },
        },
    },

    settingsItem: {
        group: 'system',
        to: 'sw.first.run.wizard.index',
        icon: 'regular-rocket',
        privilege: 'admin',
    },
});
