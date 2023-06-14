/**
 * @package admin
 *
 * @deprecated tag:v6.6.0 - Will be private
 */
export default function initMenuItems(): void {
    Shuwei.ExtensionAPI.handle('menuItemAdd', async (menuItemConfig, additionalInformation) => {
        const extension = Object.values(Shuwei.State.get('extensions'))
            .find(ext => ext.baseUrl.startsWith(additionalInformation._event_.origin));

        if (!extension) {
            throw new Error(`Extension with the origin "${additionalInformation._event_.origin}" not found.`);
        }

        await Shuwei.State.dispatch('extensionSdkModules/addModule', {
            heading: menuItemConfig.label,
            locationId: menuItemConfig.locationId,
            displaySearchBar: menuItemConfig.displaySearchBar,
            baseUrl: extension.baseUrl,
        }).then((moduleId) => {
            if (typeof moduleId !== 'string') {
                return;
            }

            Shuwei.State.commit('menuItem/addMenuItem', {
                ...menuItemConfig,
                moduleId,
            });
        });
    });
}
