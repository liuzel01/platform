/**
 * @package admin
 */

/* Is covered by E2E tests */
import { publish } from '@58shuwei-ag/admin-extension-sdk/es/channel';

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default function initializeContext(): void {
    // Handle incoming context requests from the ExtensionAPI
    Shuwei.ExtensionAPI.handle('contextCurrency', () => {
        return {
            systemCurrencyId: Shuwei.Context.app.systemCurrencyId ?? '',
            systemCurrencyISOCode: Shuwei.Context.app.systemCurrencyISOCode ?? '',
        };
    });

    Shuwei.ExtensionAPI.handle('contextEnvironment', () => {
        return Shuwei.Context.app.environment ?? 'production';
    });

    Shuwei.ExtensionAPI.handle('contextLanguage', () => {
        return {
            languageId: Shuwei.Context.api.languageId ?? '',
            systemLanguageId: Shuwei.Context.api.systemLanguageId ?? '',
        };
    });

    Shuwei.ExtensionAPI.handle('contextLocale', () => {
        return {
            fallbackLocale: Shuwei.Context.app.fallbackLocale ?? '',
            locale: Shuwei.State.get('session').currentLocale ?? '',
        };
    });

    Shuwei.ExtensionAPI.handle('contextShuweiVersion', () => {
        return Shuwei.Context.app.config.version ?? '';
    });

    Shuwei.ExtensionAPI.handle('contextModuleInformation', (_, additionalInformation) => {
        const extension = Object.values(Shuwei.State.get('extensions'))
            .find(ext => ext.baseUrl.startsWith(additionalInformation._event_.origin));

        if (!extension) {
            return {
                modules: [],
            };
        }

        // eslint-disable-next-line max-len,@typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
        const modules = Shuwei.State.getters['extensionSdkModules/getRegisteredModuleInformation'](extension.baseUrl) as Array< {
            displaySearchBar: boolean,
            heading: string,
            id: string,
            locationId: string
        }>;

        return {
            modules,
        };
    });

    // @ts-expect-error
    Shuwei.ExtensionAPI.handle('contextUserInformation', (_, { _event_ }) => {
        const appOrigin = _event_.origin;
        const extension = Object.entries(Shuwei.State.get('extensions')).find((ext) => {
            return ext[1].baseUrl.startsWith(appOrigin);
        });

        if (!extension) {
            return Promise.reject(new Error(`Could not find a extension with the given event origin "${_event_.origin}"`));
        }

        if (!extension[1]?.permissions?.read?.includes('user')) {
            return Promise.reject(new Error(`Extension "${extension[0]}" does not have the permission to read users`));
        }

        const currentUser = Shuwei.State.get('session').currentUser;

        return Promise.resolve({
            aclRoles: currentUser.aclRoles as unknown as Array<{
                name: string,
                type: string,
                id: string,
                privileges: Array<string>,
            }>,
            active: !!currentUser.active,
            admin: !!currentUser.admin,
            avatarId: currentUser.avatarId ?? '',
            email: currentUser.email ?? '',
            firstName: currentUser.firstName ?? '',
            id: currentUser.id ?? '',
            lastName: currentUser.lastName ?? '',
            localeId: currentUser.localeId ?? '',
            title: currentUser.title ?? '',
            // @ts-expect-error - type is not defined in entity directly
            type: (currentUser.type as unknown as string) ?? '',
            username: currentUser.username ?? '',
        });
    });

    Shuwei.ExtensionAPI.handle('contextAppInformation', (_, { _event_ }) => {
        const appOrigin = _event_.origin;
        const extension = Object.entries(Shuwei.State.get('extensions')).find((ext) => {
            return ext[1].baseUrl.startsWith(appOrigin);
        });

        if (!extension || !extension[0] || !extension[1]) {
            const type: 'app'|'plugin' = 'app';

            return {
                name: 'unknown',
                type: type,
                version: '0.0.0',
            };
        }

        return {
            name: extension[0],
            type: extension[1].type,
            version: extension[1].version ?? '',
        };
    });

    Shuwei.State.watch((state) => {
        return {
            languageId: state.context.api.languageId,
            systemLanguageId: state.context.api.systemLanguageId,
        };
    }, ({ languageId, systemLanguageId }, { languageId: oldLanguageId, systemLanguageId: oldSystemLanguageId }) => {
        if (languageId === oldLanguageId && systemLanguageId === oldSystemLanguageId) {
            return;
        }

        void publish('contextLanguage', {
            languageId: languageId ?? '',
            systemLanguageId: systemLanguageId ?? '',
        });
    });

    Shuwei.State.watch((state) => {
        return {
            fallbackLocale: state.context.app.fallbackLocale,
            locale: state.session.currentLocale,
        };
    }, ({ fallbackLocale, locale }, { fallbackLocale: oldFallbackLocale, locale: oldLocale }) => {
        if (fallbackLocale === oldFallbackLocale && locale === oldLocale) {
            return;
        }

        void publish('contextLocale', {
            locale: locale ?? '',
            fallbackLocale: fallbackLocale ?? '',
        });
    });
}
