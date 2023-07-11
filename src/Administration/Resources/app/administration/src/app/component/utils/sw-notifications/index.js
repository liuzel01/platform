import template from './sw-notifications.html.twig';
import './sw-notifications.scss';

const { Component } = Shuwei;

/**
 * @deprecated tag:v6.6.0 - Will be private
 * @private
 * @description
 * Wrapper element for all notifications of the administration.
 * @status ready
 * @example-type code-only
 */
Component.register('sw-notifications', {
    template,

    props: {
        position: {
            type: String,
            required: false,
            default: 'topRight',
            validator(value) {
                if (!value.length) {
                    return true;
                }
                return ['topRight', 'bottomRight'].includes(value);
            },
        },
        notificationsGap: {
            type: String,
            default: '2px',
        },
        notificationsTopGap: {
            type: String,
            default: '6px',
        },
    },

    computed: {
        notifications() {
            return Shuwei.State.getters['notification/getGrowlNotifications'];
        },

        notificationsStyle() {
            let notificationsGap = this.notificationsGap;

            if (`${parseInt(notificationsGap, 10)}` === notificationsGap) {
                notificationsGap = `${notificationsGap}px`;
            }

            if (this.position === 'bottomRight') {
                return {
                    top: 'auto',
                    right: notificationsGap,
                    bottom: notificationsGap,
                    left: 'auto',
                };
            }

            return {
                top: this.notificationsTopGap,
                right: notificationsGap,
                bottom: 'auto',
                left: 'auto',
            };
        },
    },

    methods: {
        onClose(notification) {
            Shuwei.State.commit('notification/removeGrowlNotification', notification);
        },

        handleAction(action, notification) {
            // Allow external links for example to the shuwei account or store
            if (Shuwei.Utils.string.isUrl(action.route)) {
                window.open(action.route);
                return;
            }

            if (action.route) {
                this.$router.push(action.route);
            }

            if (action.method && typeof action.method === 'function') {
                action.method.call();
            }

            this.onClose(notification);
        },
    },
});
