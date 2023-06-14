/**
 * @package admin
 */

import type { Module } from 'vuex';
import type { AppModuleDefinition } from 'src/core/service/api/app-modules.service';

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export interface ShuweiAppsState {
    apps: AppModuleDefinition[],
    selectedIds: string[],
}

const shuweiApps: Module<ShuweiAppsState, VuexRootState> = {
    namespaced: true,

    state() {
        return {
            apps: [],
            selectedIds: [],
        };
    },

    mutations: {
        setApps(state, apps: AppModuleDefinition[]) {
            state.apps = apps;
        },

        setSelectedIds(state, selectedIds: string[]) {
            state.selectedIds = selectedIds;
        },
    },
};

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default shuweiApps;
