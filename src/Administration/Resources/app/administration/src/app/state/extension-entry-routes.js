/**
 * @package admin
 *
 * @deprecated tag:v6.6.0 - Will be private
 */
export default {
    namespaced: true,
    state: {
        routes: {},
    },

    mutations: {
        addItem(state, config) {
            Shuwei.Application.view.setReactive(state.routes, config.extensionName, config);
        },
    },
};
