import type Vue from 'vue';
import type { Route, RawLocation } from 'vue-router';
import type { Extension } from '../../service/extension-store-action.service';
import template from './sw-extension-config.html.twig';
import './sw-extension-config.scss';

const { Mixin } = Shuwei;

type ComponentData = {
    salesChannelId: string|null,
    extension: Extension|null,
    fromLink: Route|null,
}

/**
 * @package merchant-services
 * @private
 */
export default Shuwei.Component.wrapComponentConfig({
    template,

    beforeRouteEnter(to, from, next) {
        next((vm) => {
            // @ts-expect-error
            vm.fromLink = from;
        });
    },

    inject: [
        'shuweiExtensionService',
    ],

    mixins: [
        Mixin.getByName('notification'),
    ],

    props: {
        namespace: {
            type: String,
            required: true,
        },
    },

    data(): ComponentData {
        return {
            salesChannelId: null,
            extension: null,
            fromLink: null,
        };
    },

    computed: {
        domain(): string {
            return `${this.namespace}.config`;
        },

        myExtensions(): Extension[] {
            return Shuwei.State.get('shuweiExtensions').myExtensions.data;
        },

        defaultThemeAsset(): string {
            return Shuwei.Filter.getByName('asset')('administration/static/img/theme/default_theme_preview.jpg');
        },

        image(): string {
            if (this.extension?.icon) {
                return this.extension.icon;
            }

            if (this.extension?.iconRaw) {
                return `data:image/png;base64, ${this.extension.iconRaw}`;
            }

            return this.defaultThemeAsset;
        },

        extensionLabel(): string {
            return this.extension?.label ?? this.namespace;
        },
    },

    created() {
        void this.createdComponent();
    },

    methods: {
        async createdComponent(): Promise<void> {
            if (!this.myExtensions.length) {
                await this.shuweiExtensionService.updateExtensionData();
            }

            this.extension = this.myExtensions.find((ext) => {
                return ext.name === this.namespace;
            }) ?? null;
        },

        async onSave(): Promise<void> {
            try {
                // @ts-expect-error
                // eslint-disable-next-line @typescript-eslint/no-unsafe-call
                await this.$refs.systemConfig.saveAll();

                this.createNotificationSuccess({
                    message: this.$tc('sw-extension-store.component.sw-extension-config.messageSaveSuccess'),
                });
            } catch (err) {
                this.createNotificationError({
                    message: err as string,
                });
            }
        },
    },
});
