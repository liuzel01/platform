import type { ShuweiClass } from 'src/core/shuwei';
import extensionStore from './extensions.store';

/**
 * @package merchant-services
 * @private
 */
export default function initState(Shuwei: ShuweiClass): void {
    Shuwei.State.registerModule('shuweiExtensions', extensionStore);

    let languageId = Shuwei.State.get('session').languageId;
    Shuwei.State._store.subscribe(async ({ type }, state): Promise<void> => {
        if (!Shuwei.Service('acl').can('system.plugin_maintain')) {
            return;
        }

        if (type === 'setAdminLocale' && state.session.languageId !== '' && languageId !== state.session.languageId) {
            // Always on page load setAdminLocale will be called once. Catch it to not load refresh extensions
            if (languageId === '') {
                languageId = state.session.languageId;
                return;
            }

            languageId = state.session.languageId;
            await Shuwei.Service('shuweiExtensionService').updateExtensionData().then();
        }
    });
}
