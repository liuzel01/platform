import 'src/app/mixin/salutation.mixin';
import { shallowMount } from '@vue/test-utils';

async function createWrapper() {
    return shallowMount({
        template: `
            <div class="sw-mock">
              <slot></slot>
            </div>
        `,
        mixins: [
            Shuwei.Mixin.getByName('salutation'),
        ],
        data() {
            return {
                name: 'sw-mock-field',
            };
        },
    }, {
        stubs: {},
        mocks: {},
        propsData: {},
        provide: {},
        attachTo: document.body,
    });
}

describe('src/app/mixin/salutation.mixin.ts', () => {
    let wrapper;
    let originalFilterGetByName;

    beforeEach(async () => {
        if (originalFilterGetByName) {
            Object.defineProperty(Shuwei.Filter, 'getByName', {
                value: jest.fn(() => {
                    return jest.fn(() => 'Salutation filter result');
                }),
            });
        } else {
            originalFilterGetByName = Shuwei.Filter.getByName;
        }

        wrapper = await createWrapper();

        await flushPromises();
    });

    afterEach(async () => {
        if (wrapper) {
            await wrapper.destroy();
        }

        await flushPromises();
    });

    it('should be a Vue.js component', () => {
        expect(wrapper.vm).toBeTruthy();
    });

    it('should compute correct salutationFilter value', () => {
        const result = wrapper.vm.salutationFilter();

        expect(result).toBe('Salutation filter result');
        expect(Shuwei.Filter.getByName).toHaveBeenCalledWith('salutation');
    });

    it('should return the correct salutation filter for entity with fallback snippet', () => {
        const result = wrapper.vm.salutation('product', 'myFallbackSnippet');

        expect(result).toBe('Salutation filter result');
        expect(Shuwei.Filter.getByName).toHaveBeenCalledWith('salutation');
    });
});
