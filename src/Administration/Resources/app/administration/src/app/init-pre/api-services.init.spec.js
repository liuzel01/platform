import initializeApiServices from 'src/app/init-pre/api-services.init';

describe('src/app/init-pre/api-services.init.ts', () => {
    /**
     * [
     *         'aclApiService',
     *         'appActionButtonService',
     *         'appCmsBlocks',
     *         'appModulesService',
     *         'appUrlChangeService',
     *         'businessEventService',
     *         'cacheApiService',
     *         'calculate-price',
     *         'cartStoreService',
     *         'checkoutStoreService',
     *         'configService',
     *         'customSnippetApiService',
     *         'customerGroupRegistrationService',
     *         'customerValidationService',
     *         'documentService',
     *         'excludedSearchTermService',
     *         'extensionSdkService',
     *         'firstRunWizardService',
     *         'flowActionService',
     *         'knownIpsService',
     *         'languagePluginService',
     *         'mailService',
     *         'mediaFolderService',
     *         'mediaService',
     *         'messageQueueService',
     *         'notificationsService',
     *         'numberRangeService',
     *         'orderDocumentApiService',
     *         'orderStateMachineService',
     *         'orderService',
     *         'productExportService',
     *         'productStreamPreviewService',
     *         'promotionSyncService',
     *         'recommendationsService',
     *         'ruleConditionsConfigApiService',
     *         'websiteService',
     *         'scheduledTaskService',
     *         'searchService',
     *         'seoUrlTemplateService',
     *         'seoUrlService',
     *         'snippetSetService',
     *         'snippetService',
     *         'stateMachineService',
     *         'contextStoreService',
     *         'storeService',
     *         'syncService',
     *         'systemConfigApiService',
     *         'tagApiService',
     *         'updateService',
     *         'userActivityApiService',
     *         'userConfigService',
     *         'userInputSanitizeService',
     *         'userRecoveryService',
     *         'userValidationService',
     *         'userService'
     *       ]
     */

    it('should initialize the api services', () => {
        expect(Shuwei.Service('aclApiService')).toBeUndefined();
        expect(Shuwei.Service('appActionButtonService')).toBeUndefined();
        expect(Shuwei.Service('appCmsBlocks')).toBeUndefined();
        expect(Shuwei.Service('appModulesService')).toBeUndefined();
        expect(Shuwei.Service('appUrlChangeService')).toBeUndefined();
        expect(Shuwei.Service('businessEventService')).toBeUndefined();
        expect(Shuwei.Service('cacheApiService')).toBeUndefined();
        expect(Shuwei.Service('calculate-price')).toBeUndefined();
        expect(Shuwei.Service('cartStoreService')).toBeUndefined();
        expect(Shuwei.Service('checkoutStoreService')).toBeUndefined();
        expect(Shuwei.Service('configService')).toBeUndefined();
        expect(Shuwei.Service('customSnippetApiService')).toBeUndefined();
        expect(Shuwei.Service('customerGroupRegistrationService')).toBeUndefined();
        expect(Shuwei.Service('customerValidationService')).toBeUndefined();
        expect(Shuwei.Service('documentService')).toBeUndefined();
        expect(Shuwei.Service('excludedSearchTermService')).toBeUndefined();
        expect(Shuwei.Service('extensionSdkService')).toBeUndefined();
        expect(Shuwei.Service('firstRunWizardService')).toBeUndefined();
        expect(Shuwei.Service('flowActionService')).toBeUndefined();
        expect(Shuwei.Service('knownIpsService')).toBeUndefined();
        expect(Shuwei.Service('languagePluginService')).toBeUndefined();
        expect(Shuwei.Service('mailService')).toBeUndefined();
        expect(Shuwei.Service('mediaFolderService')).toBeUndefined();
        expect(Shuwei.Service('mediaService')).toBeUndefined();
        expect(Shuwei.Service('messageQueueService')).toBeUndefined();
        expect(Shuwei.Service('notificationsService')).toBeUndefined();
        expect(Shuwei.Service('numberRangeService')).toBeUndefined();
        expect(Shuwei.Service('orderDocumentApiService')).toBeUndefined();
        expect(Shuwei.Service('orderStateMachineService')).toBeUndefined();
        expect(Shuwei.Service('orderService')).toBeUndefined();
        expect(Shuwei.Service('productExportService')).toBeUndefined();
        expect(Shuwei.Service('productStreamPreviewService')).toBeUndefined();
        expect(Shuwei.Service('promotionSyncService')).toBeUndefined();
        expect(Shuwei.Service('recommendationsService')).toBeUndefined();
        expect(Shuwei.Service('ruleConditionsConfigApiService')).toBeUndefined();
        expect(Shuwei.Service('websiteService')).toBeUndefined();
        expect(Shuwei.Service('scheduledTaskService')).toBeUndefined();
        expect(Shuwei.Service('searchService')).toBeUndefined();
        expect(Shuwei.Service('seoUrlTemplateService')).toBeUndefined();
        expect(Shuwei.Service('seoUrlService')).toBeUndefined();
        expect(Shuwei.Service('snippetSetService')).toBeUndefined();
        expect(Shuwei.Service('snippetService')).toBeUndefined();
        expect(Shuwei.Service('stateMachineService')).toBeUndefined();
        expect(Shuwei.Service('contextStoreService')).toBeUndefined();
        expect(Shuwei.Service('storeService')).toBeUndefined();
        expect(Shuwei.Service('syncService')).toBeUndefined();
        expect(Shuwei.Service('systemConfigApiService')).toBeUndefined();
        expect(Shuwei.Service('tagApiService')).toBeUndefined();
        expect(Shuwei.Service('updateService')).toBeUndefined();
        expect(Shuwei.Service('userActivityApiService')).toBeUndefined();
        expect(Shuwei.Service('userConfigService')).toBeUndefined();
        expect(Shuwei.Service('userInputSanitizeService')).toBeUndefined();
        expect(Shuwei.Service('userRecoveryService')).toBeUndefined();
        expect(Shuwei.Service('userValidationService')).toBeUndefined();
        expect(Shuwei.Service('userService')).toBeUndefined();

        initializeApiServices();

        expect(Shuwei.Service('aclApiService')).toBeDefined();
        expect(Shuwei.Service('appActionButtonService')).toBeDefined();
        expect(Shuwei.Service('appCmsBlocks')).toBeDefined();
        expect(Shuwei.Service('appModulesService')).toBeDefined();
        expect(Shuwei.Service('appUrlChangeService')).toBeDefined();
        expect(Shuwei.Service('businessEventService')).toBeDefined();
        expect(Shuwei.Service('cacheApiService')).toBeDefined();
        expect(Shuwei.Service('calculate-price')).toBeDefined();
        expect(Shuwei.Service('cartStoreService')).toBeDefined();
        expect(Shuwei.Service('checkoutStoreService')).toBeDefined();
        expect(Shuwei.Service('configService')).toBeDefined();
        expect(Shuwei.Service('customSnippetApiService')).toBeDefined();
        expect(Shuwei.Service('customerGroupRegistrationService')).toBeDefined();
        expect(Shuwei.Service('customerValidationService')).toBeDefined();
        expect(Shuwei.Service('documentService')).toBeDefined();
        expect(Shuwei.Service('excludedSearchTermService')).toBeDefined();
        expect(Shuwei.Service('extensionSdkService')).toBeDefined();
        expect(Shuwei.Service('firstRunWizardService')).toBeDefined();
        expect(Shuwei.Service('flowActionService')).toBeDefined();
        expect(Shuwei.Service('importExportService')).toBeDefined();
        expect(Shuwei.Service('knownIpsService')).toBeDefined();
        expect(Shuwei.Service('languagePluginService')).toBeDefined();
        expect(Shuwei.Service('mailService')).toBeDefined();
        expect(Shuwei.Service('mediaFolderService')).toBeDefined();
        expect(Shuwei.Service('mediaService')).toBeDefined();
        expect(Shuwei.Service('messageQueueService')).toBeDefined();
        expect(Shuwei.Service('notificationsService')).toBeDefined();
        expect(Shuwei.Service('numberRangeService')).toBeDefined();
        expect(Shuwei.Service('orderDocumentApiService')).toBeDefined();
        expect(Shuwei.Service('orderStateMachineService')).toBeDefined();
        expect(Shuwei.Service('orderService')).toBeDefined();
        expect(Shuwei.Service('productExportService')).toBeDefined();
        expect(Shuwei.Service('productStreamPreviewService')).toBeDefined();
        expect(Shuwei.Service('promotionSyncService')).toBeDefined();
        expect(Shuwei.Service('recommendationsService')).toBeDefined();
        expect(Shuwei.Service('ruleConditionsConfigApiService')).toBeDefined();
        expect(Shuwei.Service('websiteService')).toBeDefined();
        expect(Shuwei.Service('scheduledTaskService')).toBeDefined();
        expect(Shuwei.Service('searchService')).toBeDefined();
        expect(Shuwei.Service('seoUrlTemplateService')).toBeDefined();
        expect(Shuwei.Service('seoUrlService')).toBeDefined();
        expect(Shuwei.Service('snippetSetService')).toBeDefined();
        expect(Shuwei.Service('snippetService')).toBeDefined();
        expect(Shuwei.Service('stateMachineService')).toBeDefined();
        expect(Shuwei.Service('contextStoreService')).toBeDefined();
        expect(Shuwei.Service('storeService')).toBeDefined();
        expect(Shuwei.Service('syncService')).toBeDefined();
        expect(Shuwei.Service('systemConfigApiService')).toBeDefined();
        expect(Shuwei.Service('tagApiService')).toBeDefined();
        expect(Shuwei.Service('updateService')).toBeDefined();
        expect(Shuwei.Service('userActivityApiService')).toBeDefined();
        expect(Shuwei.Service('userConfigService')).toBeDefined();
        expect(Shuwei.Service('userInputSanitizeService')).toBeDefined();
        expect(Shuwei.Service('userRecoveryService')).toBeDefined();
        expect(Shuwei.Service('userValidationService')).toBeDefined();
        expect(Shuwei.Service('userService')).toBeDefined();
    });
});
