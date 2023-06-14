/**
 * @package merchant-services
 * @private
 */
Shuwei.Mixin.register('sw-extension-error', {
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
});
