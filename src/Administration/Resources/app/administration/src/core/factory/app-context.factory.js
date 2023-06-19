/**
 * @package admin
 *
 * @deprecated tag:v6.6.0 - Will be private
 * @module core/factory/context
 * @param {Object} context
 * @type factory
 */
export default function createContext(context = {}) {
    // set initial context
    Shuwei.State.commit('context/setAppEnvironment', process.env.NODE_ENV);
    Shuwei.State.commit('context/setAppFallbackLocale', 'zh-CN');

    // assign unknown context information
    Object.entries(context).forEach(([key, value]) => {
        Shuwei.State.commit('context/addAppValue', { key, value });
    });

    return Shuwei.Context.app;
}
