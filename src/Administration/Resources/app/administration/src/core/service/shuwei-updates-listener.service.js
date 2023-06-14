const { Application } = Shuwei;

/**
 * @package admin
 *
 * @module core/service/shuwei-updates-listener
 */

/**
 *
 * @memberOf module:core/service/shuwei-updates-listener
 * @method addShuweiUpdatesListener
 * @param loginService
 * @param serviceContainer
 */
// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default function addShuweiUpdatesListener(loginService, serviceContainer) {
    /** @var {String} localStorage token */
    let applicationRoot = null;

    loginService.addOnLoginListener(() => {
        if (!Shuwei.Service('acl').can('system.core_update')) {
            return;
        }

        serviceContainer.updateService.checkForUpdates()
            .then((response) => {
                if (response.version) {
                    createUpdatesAvailableNotification(response);
                }
            })
            .catch();
    });

    function createUpdatesAvailableNotification(response) {
        const cancelLabel =
            getApplicationRootReference().$tc('global.default.cancel');
        const updateLabel =
            getApplicationRootReference().$tc('global.notification-center.shuwei-updates-listener.updateNow');

        const notification = {
            title: getApplicationRootReference().$t(
                'global.notification-center.shuwei-updates-listener.updatesAvailableTitle',
                {
                    version: response.version,
                },
            ),
            message: getApplicationRootReference().$t(
                'global.notification-center.shuwei-updates-listener.updatesAvailableMessage',
                {
                    version: response.version,
                },
            ),
            variant: 'info',
            growl: true,
            system: true,
            actions: [{
                label: updateLabel,
                route: { name: 'sw.settings.shuwei.updates.wizard' },
            }, {
                label: cancelLabel,
            }],
            autoClose: false,
        };

        getApplicationRootReference().$store.dispatch(
            'notification/createNotification',
            notification,
        );
    }

    function getApplicationRootReference() {
        if (!applicationRoot) {
            applicationRoot = Application.getApplicationRoot();
        }

        return applicationRoot;
    }
}
