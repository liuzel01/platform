/**
 * @package admin
 */

type ServiceObject = {
    get: <SN extends keyof ServiceContainer>(serviceName: SN) => ServiceContainer[SN],
    list: () => (keyof ServiceContainer)[],
    register: <SN extends keyof ServiceContainer>(serviceName: SN, service: ServiceContainer[SN]) => void,
    registerMiddleware: typeof Shuwei.Application.addServiceProviderMiddleware,
    registerDecorator: typeof Shuwei.Application.addServiceProviderDecorator,
}

/**
 * Return the ServiceObject (Shuwei.Service().myService)
 * or direct access the services (Shuwei.Service('myService')
 */
function serviceAccessor<SN extends keyof ServiceContainer>(serviceName: SN): ServiceContainer[SN]
function serviceAccessor(): ServiceObject
function serviceAccessor<SN extends keyof ServiceContainer>(serviceName?: SN): ServiceContainer[SN] | ServiceObject {
    if (serviceName) {
        // eslint-disable-next-line @typescript-eslint/no-unsafe-return
        return Shuwei.Application.getContainer('service')[serviceName];
    }

    const serviceObject: ServiceObject = {
        // eslint-disable-next-line @typescript-eslint/no-unsafe-return
        get: (name) => Shuwei.Application.getContainer('service')[name],
        list: () => Shuwei.Application.getContainer('service').$list(),
        register: (name, service) => Shuwei.Application.addServiceProvider(name, service),
        registerMiddleware: (...args) => Shuwei.Application.addServiceProviderMiddleware(...args),
        registerDecorator: (...args) => Shuwei.Application.addServiceProviderDecorator(...args),
    };

    return serviceObject;
}

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default serviceAccessor;
