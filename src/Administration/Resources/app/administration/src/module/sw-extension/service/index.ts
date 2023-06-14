import type { SubContainer } from 'src/global.types';

import ExtensionStoreActionService from './extension-store-action.service';
import ShuweiExtensionService from './shuwei-extension.service';
import ExtensionErrorService from './extension-error.service';

const { Application } = Shuwei;

/**
 * @package merchant-services
 */
declare global {
    interface ServiceContainer extends SubContainer<'service'>{
        extensionStoreActionService: ExtensionStoreActionService,
        shuweiExtensionService: ShuweiExtensionService,
        extensionErrorService: ExtensionErrorService,
    }
}

Application.addServiceProvider('extensionStoreActionService', () => {
    return new ExtensionStoreActionService(
        Shuwei.Application.getContainer('init').httpClient,
        Shuwei.Service('loginService'),
    );
});

Application.addServiceProvider('shuweiExtensionService', () => {
    return new ShuweiExtensionService(
        Shuwei.Service('appModulesService'),
        Shuwei.Service('extensionStoreActionService'),
        Shuwei.Service('shuweiDiscountCampaignService'),
        Shuwei.Service('storeService'),
    );
});

Application.addServiceProvider('extensionErrorService', () => {
    const root = Shuwei.Application.getApplicationRoot() as Vue;

    return new ExtensionErrorService({
        FRAMEWORK__APP_LICENSE_COULD_NOT_BE_VERIFIED: {
            title: 'sw-extension.errors.appLicenseCouldNotBeVerified.title',
            message: 'sw-extension.errors.appLicenseCouldNotBeVerified.message',
            autoClose: false,
            actions: [
                {
                    label: root.$tc('sw-extension.errors.appLicenseCouldNotBeVerified.actionSetLicenseDomain'),
                    method: () => {
                        void root.$router.push({
                            name: 'sw.settings.store.index',
                        });
                    },
                },
                {
                    label: root.$tc('sw-extension.errors.appLicenseCouldNotBeVerified.actionLogin'),
                    method: () => {
                        void root.$router.push({
                            name: 'sw.extension.my-extensions.account',
                        });
                    },
                },
            ],
        },
        FRAMEWORK__APP_NOT_COMPATIBLE: {
            title: 'global.default.error',
            message: 'sw-extension.errors.appIsNotCompatible',
        },
    }, {
        title: 'global.default.error',
        message: 'global.notification.unspecifiedSaveErrorMessage',
    });
});
