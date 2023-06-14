/**
 * @package admin
 *
 * @deprecated tag:v6.6.0 - Will be private
 */
export default function initializeExtensionComponentSections(): void {
    // Handle incoming ExtensionComponentRenderer requests from the ExtensionAPI
    Shuwei.ExtensionAPI.handle('uiComponentSectionRenderer', (componentConfig) => {
        Shuwei.State.commit('extensionComponentSections/addSection', componentConfig);
    });
}
