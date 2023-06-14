/**
 * @package admin
 *
 * @deprecated tag:v6.6.0 - Will be private
 */
export default {
    namespaced: true,
    state: {
        isExpanded: true,
        expandedEntries: [],
        adminModuleNavigation: [],
    },

    mutations: {
        clearExpandedMenuEntries(state) {
            state.expandedEntries = [];
        },

        expandMenuEntry(state, payload) {
            state.expandedEntries.push(payload);
        },

        collapseMenuEntry(state, payload) {
            state.expandedEntries = state.expandedEntries.filter((item) => {
                return item !== payload;
            });
        },

        collapseSidebar(state) {
            state.isExpanded = false;
        },

        expandSidebar(state) {
            state.isExpanded = true;
        },

        setAdminModuleNavigation(state, navigation) {
            state.adminModuleNavigation = navigation;
        },
    },

    getters: {
        appModuleNavigation(state, getters, rootState) {
            const menuService = Shuwei.Service('menuService');

            return menuService?.getNavigationFromApps(rootState.shuweiApps.apps);
        },
    },
};
