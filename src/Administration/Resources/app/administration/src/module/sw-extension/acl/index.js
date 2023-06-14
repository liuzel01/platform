/**
 * @package merchant-services
 */
Shuwei.Service('privileges')
    .addPrivilegeMappingEntry({
        category: 'additional_permissions',
        parent: null,
        key: 'system',
        roles: {
            extension_store: {
                privileges: [],
                dependencies: [
                    'system.plugin_maintain',
                ],
            },
        },
    });
