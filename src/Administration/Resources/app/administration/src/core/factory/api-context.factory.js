/**
 * @package admin
 *
 * @deprecated tag:v6.6.0 - Will be private
 * @module core/factory/context
 * @param {Object} context
 * @type factory
 */
export default function createContext(context = {}) {
    const Defaults = Shuwei.Defaults;
    const isDevMode = (process.env.NODE_ENV !== 'production');
    const installationPath = getInstallationPath(context, isDevMode);
    const apiPath = `${installationPath}/api`;

    const languageId = localStorage.getItem('sw-admin-current-language') || Defaults.systemLanguageId;

    // set initial context
    Shuwei.State.commit('context/setApiInstallationPath', installationPath);
    Shuwei.State.commit('context/setApiApiPath', apiPath);
    Shuwei.State.commit('context/setApiApiResourcePath', `${apiPath}`);
    Shuwei.State.commit('context/setApiAssetsPath', getAssetsPath(context.assetPath, isDevMode));
    Shuwei.State.commit('context/setApiLanguageId', languageId);
    Shuwei.State.commit('context/setApiInheritance', false);

    if (isDevMode) {
        Shuwei.State.commit('context/setApiSystemLanguageId', Defaults.systemLanguageId);
        Shuwei.State.commit('context/setApiLiveVersionId', Defaults.versionId);
    }

    // assign unknown context information
    Object.entries(context).forEach(([key, value]) => {
        Shuwei.State.commit('context/addApiValue', { key, value });
    });

    return Shuwei.Context.api;
}

/**
 * Provides the installation path of the application. The path provides the scheme, host and sub directory.
 *
 * @param {Object} context
 * @param {Boolean} isDevMode
 * @returns {string}
 */
function getInstallationPath(context, isDevMode) {
    if (isDevMode) {
        return '';
    }

    let fullPath = '';
    if (context.schemeAndHttpHost?.length) {
        fullPath = `${context.schemeAndHttpHost}${context.basePath}`;
    }

    return fullPath;
}

/**
 * Provides the path to the assets directory.
 *
 * @param {String} installationPath
 * @param {Boolean} isDevMode
 * @returns {string}
 */
function getAssetsPath(installationPath, isDevMode) {
    if (isDevMode) {
        return '/bundles/';
    }

    return `${installationPath}/bundles/`;
}
