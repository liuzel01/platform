import template from './sw-shortcut-overview.html.twig';
import './sw-shortcut-overview.scss';

const { Component } = Shuwei;
const utils = Shuwei.Utils;

/**
 * @deprecated tag:v6.6.0 - Will be private
 */
Component.register('sw-shortcut-overview', {
    template,

    shortcuts: {
        '?': 'onOpenShortcutOverviewModal',
    },

    data() {
        return {
            showShortcutOverviewModal: false,
        };
    },

    computed: {
        sections() {
            return {
                addingItems: [

                ],
                navigation: [
                    {
                        id: utils.createId(),
                        title: this.$tc('sw-shortcut-overview.functionGoToDashboard'),
                        content: this.$tc('sw-shortcut-overview.keyboardShortcutGoToDashboard'),
                    },
                    {
                        id: utils.createId(),
                        title: this.$tc('sw-shortcut-overview.functionGoToMedia'),
                        content: this.$tc('sw-shortcut-overview.keyboardShortcutGoToMedia'),
                    },
                    {
                        id: utils.createId(),
                        title: this.$tc('sw-shortcut-overview.functionGoToPlugins'),
                        content: this.$tc('sw-shortcut-overview.keyboardShortcutGoToPlugins'),
                        privilege: 'system.plugin_maintain',
                    },

                ],

                specialShortcuts: [
                    {
                        id: utils.createId(),
                        title: this.$tc('sw-shortcut-overview.functionSpecialShortcutFocusSearch'),
                        content: this.$tc('sw-shortcut-overview.keyboardShortcutSpecialShortcutFocusSearch'),
                    },
                    {
                        id: utils.createId(),
                        title: this.$tc('sw-shortcut-overview.functionSpecialShortcutShortcutListing'),
                        content: this.$tc('sw-shortcut-overview.keyboardShortcutSpecialShortcutShortcutListing'),
                    },
                    {
                        id: utils.createId(),
                        title: this.$tc('sw-shortcut-overview.functionSpecialShortcutSaveDetailViewWindows'),
                        content: this.$tc('sw-shortcut-overview.keyboardShortcutSpecialShortcutSaveDetailViewWindows'),
                    },
                    {
                        id: utils.createId(),
                        title: this.$tc('sw-shortcut-overview.functionSpecialShortcutSaveDetailViewMac'),
                        content: this.$tc('sw-shortcut-overview.keyboardShortcutSpecialShortcutSaveDetailViewMac'),
                    },
                    {
                        id: utils.createId(),
                        title: this.$tc('sw-shortcut-overview.functionSpecialShortcutSaveDetailViewLinux'),
                        content: this.$tc('sw-shortcut-overview.keyboardShortcutSpecialShortcutSaveDetailViewLinux'),
                    },
                    {
                        id: utils.createId(),
                        title: this.$tc('sw-shortcut-overview.functionSpecialShortcutCancelDetailView'),
                        content: this.$tc('sw-shortcut-overview.keyboardShortcutSpecialShortcutCancelDetailView'),
                    },
                    {
                        id: utils.createId(),
                        title: this.$tc('sw-shortcut-overview.functionSpecialShortcutClearCacheWindows'),
                        content: this.$tc('sw-shortcut-overview.keyboardShortcutSpecialShortcutClearCacheWindows'),
                        privilege: 'system.clear_cache',
                    },
                    {
                        id: utils.createId(),
                        title: this.$tc('sw-shortcut-overview.functionSpecialShortcutClearCacheMac'),
                        content: this.$tc('sw-shortcut-overview.keyboardShortcutSpecialShortcutClearCacheMac'),
                        privilege: 'system.clear_cache',
                    },
                    {
                        id: utils.createId(),
                        title: this.$tc('sw-shortcut-overview.functionSpecialShortcutClearCacheLinux'),
                        content: this.$tc('sw-shortcut-overview.keyboardShortcutSpecialShortcutClearCacheLinux'),
                        privilege: 'system.clear_cache',
                    },
                ],
            };
        },
    },

    methods: {
        onOpenShortcutOverviewModal() {
            this.showShortcutOverviewModal = true;
        },

        onCloseShortcutOverviewModal() {
            this.showShortcutOverviewModal = false;
        },
    },
});
