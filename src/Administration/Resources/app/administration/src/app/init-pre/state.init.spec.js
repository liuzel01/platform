import initState from 'src/app/init-pre/state.init';

describe('src/app/init-pre/state.init.ts', () => {
    initState();

    it('should contain all state methods', () => {
        expect(Shuwei.State._store).toBeDefined();
        expect(Shuwei.State.list).toBeDefined();
        expect(Shuwei.State.get).toBeDefined();
        expect(Shuwei.State.getters).toBeDefined();
        expect(Shuwei.State.commit).toBeDefined();
        expect(Shuwei.State.dispatch).toBeDefined();
        expect(Shuwei.State.watch).toBeDefined();
        expect(Shuwei.State.subscribe).toBeDefined();
        expect(Shuwei.State.subscribeAction).toBeDefined();
        expect(Shuwei.State.registerModule).toBeDefined();
        expect(Shuwei.State.unregisterModule).toBeDefined();
    });

    it('should initialized all state modules', () => {
        expect(Shuwei.State.list()).toHaveLength(21);

        expect(Shuwei.State.get('notification')).toBeDefined();
        expect(Shuwei.State.get('session')).toBeDefined();
        expect(Shuwei.State.get('system')).toBeDefined();
        expect(Shuwei.State.get('adminMenu')).toBeDefined();
        expect(Shuwei.State.get('licenseViolation')).toBeDefined();
        expect(Shuwei.State.get('context')).toBeDefined();
        expect(Shuwei.State.get('error')).toBeDefined();
        expect(Shuwei.State.get('settingsItems')).toBeDefined();
        expect(Shuwei.State.get('shuweiApps')).toBeDefined();
        expect(Shuwei.State.get('extensionEntryRoutes')).toBeDefined();
        expect(Shuwei.State.get('marketing')).toBeDefined();
        expect(Shuwei.State.get('extensionComponentSections')).toBeDefined();
        expect(Shuwei.State.get('extensions')).toBeDefined();
        expect(Shuwei.State.get('tabs')).toBeDefined();
        expect(Shuwei.State.get('menuItem')).toBeDefined();
        expect(Shuwei.State.get('extensionSdkModules')).toBeDefined();
        expect(Shuwei.State.get('modals')).toBeDefined();
        expect(Shuwei.State.get('extensionMainModules')).toBeDefined();
        expect(Shuwei.State.get('actionButtons')).toBeDefined();
        expect(Shuwei.State.get('ruleConditionsConfig')).toBeDefined();
        expect(Shuwei.State.get('sdkLocation')).toBeDefined();
    });
});
