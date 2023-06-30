// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default function initializeShortcutService() {
    const factoryContainer = Shuwei.Application.getContainer('factory');
    // eslint-disable-next-line @typescript-eslint/no-unsafe-assignment
    const shortcutFactory = factoryContainer.shortcut;
    // eslint-disable-next-line @typescript-eslint/no-unsafe-assignment
    const shortcutService = Shuwei.Service('shortcutService');
    const loginService = Shuwei.Service('loginService');

    // Register default Shortcuts
    const defaultShortcuts = defaultShortcutMap();
    defaultShortcuts.forEach((sc) => {
        // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
        shortcutFactory.register(sc.combination, sc.path);
    });

    // Initializes the global event listener
    if (loginService.isLoggedIn()) {
        // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
        shortcutService.startEventListener();
    } else {
        loginService.addOnTokenChangedListener(() => {
            // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
            shortcutService.startEventListener();
        });
    }

    // Release global event listener on logout
    loginService.addOnLogoutListener(() => {
        // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
        shortcutService.stopEventListener();
    });

    // eslint-disable-next-line @typescript-eslint/no-unsafe-return
    return shortcutFactory;
}

function defaultShortcutMap() {
    return [
        // Add an entity
        { combination: 'AR', path: '/sw/settings/rule/create' },

        // Go to ...
        { combination: 'GH', path: '/sw/dashboard/index' },
        { combination: 'GME', path: '/sw/media/index' },
        { combination: 'GS', path: '/sw/settings/index' },
        { combination: 'GSN', path: '/sw/settings/snippet/index' },
        { combination: 'GSR', path: '/sw/settings/rule/index' },
        { combination: 'GA', path: '/sw/extension/my-extensions/listing/app' },
    ];
}
