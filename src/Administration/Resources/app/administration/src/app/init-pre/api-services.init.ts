/**
 * @package admin
 */

const apiServices = Shuwei._private.ApiServices();

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default function initializeApiServices() {
    // Add custom api service providers
    apiServices.forEach((ApiService) => {
        const factoryContainer = Shuwei.Application.getContainer('factory');
        const initContainer = Shuwei.Application.getContainer('init');

        const apiServiceFactory = factoryContainer.apiService;
        // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-assignment
        const service = new ApiService(initContainer.httpClient, Shuwei.Service('loginService'));
        // eslint-disable-next-line @typescript-eslint/no-unsafe-member-access
        const serviceName = service.name as keyof ServiceContainer;
        // eslint-disable-next-line @typescript-eslint/no-unsafe-argument
        apiServiceFactory.register(serviceName, service);

        Shuwei.Application.addServiceProvider(serviceName, () => {
            // eslint-disable-next-line @typescript-eslint/no-unsafe-return
            return service;
        });
    });
}
