import template from './sw-first-run-wizard-defaults.html.twig';
import './sw-first-run-wizard-defaults.scss';

/**
 * @package merchant-services
 * @deprecated tag:v6.6.0 - Will be private
 */
// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default {
    template,

    inject: ['repositoryFactory'],

    data() {
        return {
            isLoading: false,
            defaultWebsiteCardLoaded: false,
            website: null,
            configData: {
                null: {
                    'core.defaultWebsite.website': [],
                    'core.defaultWebsite.active': true,
                    'core.defaultWebsite.visibility': {},
                },
            },
        };
    },

    computed: {
        websiteRepository() {
            return this.repositoryFactory.create('website');
        },

        buttonConfig() {
            return [
                {
                    key: 'back',
                    label: this.$tc('sw-first-run-wizard.general.buttonBack'),
                    position: 'left',
                    variant: null,
                    action: 'sw.first.run.wizard.index.data-import',
                    disabled: false,
                }, {
                    key: 'next',
                    label: this.$tc('sw-first-run-wizard.general.buttonNext'),
                    position: 'right',
                    variant: 'primary',
                    action: this.nextAction.bind(this),
                    disabled: !this.defaultWebsiteCardLoaded,
                },
            ];
        },
    },

    watch: {
        buttonConfig: {
            handler() {
                this.updateButtons();
            },
            deep: true,
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.updateButtons();
            this.setTitle();
        },

        setTitle() {
            this.$emit('frw-set-title', this.$tc('sw-first-run-wizard.defaults.modalTitle'));
        },

        async nextAction() {
            this.isLoading = true;

            await this.$refs.defaultWebsiteCard.saveWebsiteVisibilityConfig();

            this.isLoading = false;
            this.$emit('frw-redirect', 'sw.first.run.wizard.index.mailer.selection');
        },

        updateButtons() {
            this.$emit('buttons-update', this.buttonConfig);
        },

        updateWebsite(website) {
            this.website = website;
        },
    },
};
