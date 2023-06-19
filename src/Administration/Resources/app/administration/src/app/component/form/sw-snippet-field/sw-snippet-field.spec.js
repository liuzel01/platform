/**
 * @package admin
 */

import { createLocalVue, shallowMount } from '@vue/test-utils';
import uuid from 'src/../test/_helper_/uuid';
import 'src/app/component/base/sw-icon';
import 'src/app/component/form/sw-snippet-field';
import 'src/app/component/form/sw-field';
import 'src/app/component/form/sw-text-field';
import 'src/app/component/form/field-base/sw-contextual-field';
import 'src/app/component/form/field-base/sw-block-field';
import 'src/app/component/form/field-base/sw-base-field';
import 'src/app/component/form/field-base/sw-field-error';

async function createWrapper(systemLanguageIso = '', translations = [], customOptions = {}) {
    const localVue = createLocalVue();
    localVue.directive('tooltip', {});

    return shallowMount(await Shuwei.Component.build('sw-snippet-field'), {
        localVue,
        propsData: {
            snippet: 'test.snippet',
        },
        stubs: {
            'sw-field': await Shuwei.Component.build('sw-field'),
            'sw-text-field': await Shuwei.Component.build('sw-text-field'),
            'sw-contextual-field': await Shuwei.Component.build('sw-contextual-field'),
            'sw-block-field': await Shuwei.Component.build('sw-block-field'),
            'sw-base-field': await Shuwei.Component.build('sw-base-field'),
            'sw-field-error': await Shuwei.Component.build('sw-field-error'),
            'sw-modal': true,
            'sw-loader': true,
            'sw-icon': true,
            'sw-snippet-field-edit-modal': true,
        },
        provide: {
            validationService: {},
            repositoryFactory: {
                create: (entity) => ({
                    search: () => {
                        if (entity === 'snippet_set') {
                            return Promise.resolve(createEntityCollection([
                                {
                                    name: 'Base zh-CN',
                                    iso: 'zh-CN',
                                    id: uuid.get('zh-CN'),
                                },
                                {
                                    name: 'Base en-US',
                                    iso: 'en-US',
                                    id: uuid.get('en-US'),
                                },
                            ]));
                        }

                        if (entity === 'language') {
                            return Promise.resolve(createEntityCollection([
                                {
                                    name: 'default language',
                                    locale: {
                                        code: systemLanguageIso,
                                    },
                                    id: uuid.get('default language'),
                                },
                            ]));
                        }

                        return Promise.resolve([]);
                    },
                }),
            },
            snippetSetService: {
                getCustomList: () => {
                    return Promise.resolve({
                        total: translations.length,
                        data: {
                            'test.snippet': translations,
                        },
                    });
                },
            },
        },
        ...customOptions,
    });
}

function createEntityCollection(entities = []) {
    return new Shuwei.Data.EntityCollection('collection', 'collection', {}, null, entities);
}

describe('src/app/component/form/sw-snippet-field', () => {
    it('should be a Vue.JS component', async () => {
        const wrapper = await createWrapper();
        expect(wrapper.vm).toBeTruthy();
    });

    it('should show admin language translation of snippet field', async () => {
        Shuwei.State.get('session').currentLocale = 'en-US';

        const wrapper = await createWrapper('zh-CN', [{
            author: 'testUser',
            id: null,
            value: 'english',
            origin: null,
            resetTo: 'english',
            translationKey: 'test.snippet',
            setId: uuid.get('zh-CN'),
        }, {
            author: 'testUser',
            id: null,
            value: 'deutsch',
            origin: null,
            resetTo: 'deutsch',
            translationKey: 'test.snippet',
            setId: uuid.get('en-US'),
        }]);

        await flushPromises();

        const textField = wrapper.find('input');
        expect(textField.element.value).toBe('deutsch');
    });

    it('should show system default language translation of snippet field', async () => {
        Shuwei.State.get('session').currentLocale = 'nl-NL';

        const wrapper = await createWrapper('en-US', [{
            author: 'testUser',
            id: null,
            value: 'english',
            origin: null,
            resetTo: 'english',
            translationKey: 'test.snippet',
            setId: uuid.get('zh-CN'),
        }, {
            author: 'testUser',
            id: null,
            value: 'deutsch',
            origin: null,
            resetTo: 'deutsch',
            translationKey: 'test.snippet',
            setId: uuid.get('en-US'),
        }]);

        await flushPromises();

        const textField = wrapper.find('input');
        expect(textField.element.value).toBe('deutsch');
    });

    it('should show zh-CN language translation of snippet field', async () => {
        Shuwei.State.get('session').currentLocale = 'nl-NL';

        const wrapper = await createWrapper('nl-NL', [{
            author: 'testUser',
            id: null,
            value: 'english',
            origin: null,
            resetTo: 'english',
            translationKey: 'test.snippet',
            setId: uuid.get('zh-CN'),
        }, {
            author: 'testUser',
            id: null,
            value: 'deutsch',
            origin: null,
            resetTo: 'deutsch',
            translationKey: 'test.snippet',
            setId: uuid.get('en-US'),
        }]);

        await flushPromises();

        const textField = wrapper.find('input');
        expect(textField.element.value).toBe('english');
    });

    it('should show snippet key as fallback', async () => {
        Shuwei.State.get('session').currentLocale = 'nl-NL';

        const wrapper = await createWrapper('nl-NL', []);

        await flushPromises();

        const textField = wrapper.find('input');
        expect(textField.element.value).toBe('test.snippet');
    });

    it('should display and hide edit modal', async () => {
        Shuwei.State.get('session').currentLocale = 'zh-CN';
        Shuwei.State.get('session').currentUser = {
            username: 'testUser',
        };

        const wrapper = await createWrapper('zh-CN', []);

        await flushPromises();

        expect(wrapper.find('sw-snippet-field-edit-modal-stub').exists()).toBe(false);

        wrapper.get('.sw-snippet-field__icon').vm.$emit('click');
        await wrapper.vm.$nextTick();

        expect(wrapper.find('sw-snippet-field-edit-modal-stub').exists()).toBe(true);

        wrapper.vm.closeEditModal();
        await wrapper.vm.$nextTick();

        expect(wrapper.find('sw-snippet-field-edit-modal-stub').exists()).toBe(false);
    });
});
