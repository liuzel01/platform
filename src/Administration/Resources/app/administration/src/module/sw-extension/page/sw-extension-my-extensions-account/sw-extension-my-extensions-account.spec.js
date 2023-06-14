import { createLocalVue, shallowMount } from '@vue/test-utils';
import swExtensionMyExtensionsAccount from 'src/module/sw-extension/page/sw-extension-my-extensions-account';
import 'src/app/component/meteor/sw-meteor-card';
import 'src/app/component/base/sw-button';
import extensionStore from 'src/module/sw-extension/store/extensions.store';

const userInfo = {
    avatarUrl: 'https://avatar.url',
    email: 'max@muster.com',
    name: 'Max Muster',
};

Shuwei.Component.register('sw-extension-my-extensions-account', swExtensionMyExtensionsAccount);

async function createWrapper() {
    const localVue = createLocalVue();
    localVue.filter('asset', key => key);

    return shallowMount(await Shuwei.Component.build('sw-extension-my-extensions-account'), {
        localVue,
        stubs: {
            'sw-avatar': true,
            'sw-loader': true,
            'sw-meteor-card': await Shuwei.Component.build('sw-meteor-card'),
            'sw-button': await Shuwei.Component.build('sw-button'),
            'sw-text-field': {
                props: ['value'],
                template: `
                    <input type="text" :value="value" @input="$emit('input', $event.target.value)" />
                `,
            },
            'sw-password-field': {
                props: ['value'],
                template: `
<input type="password" :value="value" @input="$emit('input', $event.target.value)" />
`,
            },
            'sw-skeleton': true,
        },
        provide: {
            shuweiExtensionService: {
                checkLogin: () => {
                    return Promise.resolve({
                        userInfo,
                    });
                },
            },
            systemConfigApiService: {
                getValues: () => {
                    return Promise.resolve({
                        'core.store.apiUri': 'https://api.shuwei.com',
                        'core.store.licenseHost': 'sw6.test.shuwei.in',
                        'core.store.shopSecret': 'very.s3cret',
                    });
                },
            },
            storeService: {
                login: (shuweiId, password) => {
                    if (shuweiId !== 'max@muster.com') {
                        return Promise.reject();
                    }
                    if (password !== 'v3ryS3cret') {
                        return Promise.reject();
                    }

                    Shuwei.State.get('shuweiExtensions').userInfo = userInfo;

                    return Promise.resolve();
                },
                logout: () => {
                    Shuwei.State.get('shuweiExtensions').userInfo = null;

                    return Promise.resolve();
                },
            },
        },
    });
}

/**
 * @package merchant-services
 */
describe('src/module/sw-extension/page/sw-extension-my-extensions-account', () => {
    /** @type Wrapper */
    let wrapper;

    beforeAll(async () => {
        Shuwei.State.registerModule('shuweiExtensions', extensionStore);
    });

    beforeEach(async () => {
        wrapper = await createWrapper();

        Shuwei.State.get('shuweiExtensions').userInfo = null;
    });

    afterEach(async () => {
        if (wrapper) await wrapper.destroy();
    });

    it('should be a Vue.JS component', async () => {
        expect(wrapper.vm).toBeTruthy();
    });

    it('should show the login fields when not logged in', async () => {
        const shuweiIdField = wrapper.find('.sw-extension-my-extensions-account__shuwei-id-field');
        const passwordField = wrapper.find('.sw-extension-my-extensions-account__password-field');
        const loginButton = wrapper.find('.sw-extension-my-extensions-account__login-button');

        // check if fields exists when user is not logged in
        expect(shuweiIdField.isVisible()).toBe(true);
        expect(passwordField.isVisible()).toBe(true);
        expect(loginButton.isVisible()).toBe(true);
    });

    it('should login when user clicks login', async () => {
        // check if login status is not visible
        let loginStatus = wrapper.find('.sw-extension-my-extensions-account__wrapper-content-login-status-id');
        expect(loginStatus.exists()).toBe(false);

        // get fields
        const shuweiIdField = wrapper.find('.sw-extension-my-extensions-account__shuwei-id-field');
        const passwordField = wrapper.find('.sw-extension-my-extensions-account__password-field');
        const loginButton = wrapper.find('.sw-extension-my-extensions-account__login-button');

        // enter credentials
        await shuweiIdField.setValue('max@muster.com');
        await passwordField.setValue('v3ryS3cret');

        // login
        await loginButton.trigger('click');
        await flushPromises();

        // check if layout switches
        loginStatus = wrapper.find('.sw-extension-my-extensions-account__wrapper-content-login-status-id');
        expect(loginStatus.exists()).toBe(true);
        expect(loginStatus.text()).toBe('max@muster.com');
    });

    it('should show the logged in view when logged in', async () => {
        Shuwei.State.get('shuweiExtensions').userInfo = userInfo;

        // create component with logged in view
        wrapper = await createWrapper();
        await wrapper.vm.$nextTick();
        await wrapper.vm.$nextTick();

        // check if layout shows the logged in information
        const loginStatus = wrapper.find('.sw-extension-my-extensions-account__wrapper-content-login-status-id');
        expect(loginStatus.exists()).toBe(true);
        expect(loginStatus.text()).toBe('max@muster.com');
    });

    it('should logout when user clicks logout button', async () => {
        Shuwei.State.get('shuweiExtensions').userInfo = userInfo;

        // create component with logged in view
        wrapper = await createWrapper();
        await wrapper.vm.$nextTick();
        await wrapper.vm.$nextTick();

        // check if logout button exists
        let logoutButton = wrapper.find('.sw-extension-my-extensions-account__logout-button');
        expect(logoutButton.exists()).toBe(true);

        // click on logout
        await logoutButton.trigger('click');

        // check if logout button disappears
        logoutButton = wrapper.find('.sw-extension-my-extensions-account__logout-button');
        expect(logoutButton.exists()).toBe(false);

        // check if user is sees login view
        const loginButton = wrapper.find('.sw-extension-my-extensions-account__login-button');
        expect(loginButton.exists()).toBe(true);
    });
});
