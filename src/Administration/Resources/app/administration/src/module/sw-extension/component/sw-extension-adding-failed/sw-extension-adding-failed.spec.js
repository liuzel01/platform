import { shallowMount } from '@vue/test-utils';
import swExtensionAddingFailed from 'src/module/sw-extension/component/sw-extension-adding-failed';
import 'src/app/component/base/sw-button';
import 'src/app/component/base/sw-circle-icon';
import 'src/app/component/base/sw-label';
import extensionStore from 'src/module/sw-extension/store/extensions.store';
import ShuweiExtensionService from 'src/module/sw-extension/service/shuwei-extension.service';

Shuwei.Component.register('sw-extension-adding-failed', swExtensionAddingFailed);
Shuwei.State.registerModule('shuweiExtensions', extensionStore);

async function createWrapper() {
    const shuweiExtensionService = new ShuweiExtensionService();

    return shallowMount(await Shuwei.Component.build('sw-extension-adding-failed'), {
        stubs: {
            'sw-button': await Shuwei.Component.build('sw-button'),
            'sw-circle-icon': await Shuwei.Component.build('sw-circle-icon'),
            'sw-label': await Shuwei.Component.build('sw-label'),
            'sw-icon': true,
            i18n: true,
        },
        propsData: {
            extensionName: 'test-app',
        },
        provide: {
            shuweiExtensionService,
        },
    });
}

/**
 * @package merchant-services
 */
describe('src/module/sw-extension-component/sw-extension-adding-failed', () => {
    it('passes correct props to sw-circle-icon', async () => {
        const wrapper = await createWrapper();

        expect(wrapper.get('.sw-circle-icon').props('variant')).toBe('danger');
        expect(wrapper.get('.sw-circle-icon').props('size')).toBe(72);
        expect(wrapper.get('.sw-circle-icon').props('iconName')).toBe('regular-times-circle-s');
    });

    it('has a primary block button', async () => {
        Shuwei.State.commit('shuweiExtensions/myExtensions', []);

        const wrapper = await createWrapper();

        const closeButton = wrapper.get('button.sw-button');

        expect(closeButton.props('variant')).toBe('primary');
        expect(closeButton.props('block')).toBe(true);
    });

    it('emits close if close button is clicked', async () => {
        Shuwei.State.commit('shuweiExtensions/myExtensions', []);

        const wrapper = await createWrapper();

        await wrapper.get('button.sw-button').trigger('click');

        expect(wrapper.emitted().close).toBeTruthy();
    });

    it('renders all information if extension is rent', async () => {
        Shuwei.State.commit('shuweiExtensions/myExtensions', [{
            name: 'test-app',
            storeLicense: {
                variant: 'rent',
            },
        }]);

        const wrapper = await createWrapper(true);

        expect(wrapper.get('.sw-extension-adding-failed__text-licence-cancellation').text()).toBe('sw-extension-store.component.sw-extension-adding-failed.installationFailed.notificationLicense');
    });

    it('does not render additional information if the license is not a subscription', async () => {
        Shuwei.State.commit('shuweiExtensions/myExtensions', [{
            name: 'test-app',
            storeLicense: {
                variant: 'buy',
            },
        }]);

        const wrapper = await createWrapper();

        expect(wrapper.find('.sw-extension-installation-failed__text-licence-cancellation')
            .exists())
            .toBe(false);
        expect(wrapper.find('h3').text())
            .toBe(
                'sw-extension-store.component.sw-extension-adding-failed.installationFailed.titleFailure',
            );
        expect(wrapper.find('p').text())
            .toBe(
                'sw-extension-store.component.sw-extension-adding-failed.installationFailed.textProblem',
            );
    });

    // eslint-disable-next-line max-len
    it('does not render additional information about licenses and uses general failure text if extension is not licensed', async () => {
        Shuwei.State.commit('shuweiExtensions/myExtensions', []);

        const wrapper = await createWrapper(true);

        expect(wrapper.find('.sw-extension-installation-failed__text-licence-cancellation')
            .exists()).toBe(false);
        expect(wrapper.find('h3')
            .text()).toBe('sw-extension-store.component.sw-extension-adding-failed.titleFailure');
        expect(wrapper.find('p')
            .text()).toBe('sw-extension-store.component.sw-extension-adding-failed.textProblem');
    });
});
