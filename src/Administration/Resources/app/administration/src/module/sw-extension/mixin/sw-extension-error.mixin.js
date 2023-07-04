import { defineComponent } from 'vue';

/**
 * @package merchant-services
 * @private
 */
export default Shuwei.Mixin.register('sw-extension-error', defineComponent({
    mixins: [Shuwei.Mixin.getByName('notification')],

    methods: {
        showExtensionErrors(errorResponse) {
            Shuwei.Service('extensionErrorService')
                .handleErrorResponse(errorResponse, this)
                .forEach((notification) => {
                    this.createNotificationError(notification);
                });
        },
    },
}));
