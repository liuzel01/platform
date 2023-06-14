/**
 * @package admin
 *
 * @deprecated tag:v6.6.0 - Will be private
 */
export default function initMainModules(): void {
    Shuwei.ExtensionAPI.handle('mainModuleAdd', async (mainModuleConfig, additionalInformation) => {
        const extensionName = Object.keys(Shuwei.State.get('extensions'))
            .find(key => Shuwei.State.get('extensions')[key].baseUrl.startsWith(additionalInformation._event_.origin));

        if (!extensionName) {
            throw new Error(`Extension with the origin "${additionalInformation._event_.origin}" not found.`);
        }

        const extension = Shuwei.State.get('extensions')?.[extensionName];

        await Shuwei.State.dispatch('extensionSdkModules/addModule', {
            heading: mainModuleConfig.heading,
            locationId: mainModuleConfig.locationId,
            displaySearchBar: mainModuleConfig.displaySearchBar ?? true,
            baseUrl: extension.baseUrl,
        }).then((moduleId) => {
            if (typeof moduleId !== 'string') {
                return;
            }

            Shuwei.State.commit('extensionMainModules/addMainModule', {
                extensionName,
                moduleId,
            });
        });
    });

    Shuwei.ExtensionAPI.handle('smartBarButtonAdd', (configuration) => {
        Shuwei.State.commit('extensionSdkModules/addSmartBarButton', configuration);
    });
}
