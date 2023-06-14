/**
 * @package admin
 */

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default async function initializeLocaleService() {
    const factoryContainer = Shuwei.Application.getContainer('factory');
    const localeFactory = factoryContainer.locale;

    // Register default snippets
    localeFactory.register('zh-CN', {});
    localeFactory.register('en-US', {});

    // eslint-disable-next-line @typescript-eslint/no-unsafe-assignment
    const snippetService = Shuwei.Service('snippetService');

    if (snippetService) {
        // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
        await snippetService.getSnippets(localeFactory);
    }

    return localeFactory;
}
