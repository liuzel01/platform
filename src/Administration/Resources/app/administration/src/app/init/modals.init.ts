/**
 * @package admin
 *
 * @deprecated tag:v6.6.0 - Will be private
 */
export default function initializeModal(): void {
    // eslint-disable-next-line @typescript-eslint/require-await
    Shuwei.ExtensionAPI.handle('uiModalOpen', async (modalConfig, { _event_ }) => {
        const extension = Object.values(Shuwei.State.get('extensions'))
            .find(ext => ext.baseUrl.startsWith(_event_.origin));

        if (!extension) {
            throw new Error(`Extension with the origin "${_event_.origin}" not found.`);
        }

        Shuwei.State.commit('modals/openModal', {
            closable: true,
            showHeader: true,
            variant: 'default',
            baseUrl: extension.baseUrl,
            ...modalConfig,
        });
    });

    Shuwei.ExtensionAPI.handle('uiModalClose', ({ locationId }) => {
        Shuwei.State.commit('modals/closeModal', locationId);
    });
}
