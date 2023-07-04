import template from './sw-first-run-wizard-shuwei-account.html.twig';
import './sw-first-run-wizard-shuwei-account.scss';

/**
 * @package merchant-services
 */
// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default {
    template,

    inject: ['firstRunWizardService'],

    data() {
        return {
            shuweiId: '',
            password: '',
            accountError: false,
        };
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.setTitle();
            this.updateButtons();
        },

        setTitle() {
            this.$emit('frw-set-title', this.$tc('sw-first-run-wizard.shuweiAccount.modalTitle'));
        },

        updateButtons() {
            const buttonConfig = [
                {
                    key: 'back',
                    label: this.$tc('sw-first-run-wizard.general.buttonBack'),
                    position: 'left',
                    variant: null,
                    action: 'sw.first.run.wizard.index.plugins',
                    disabled: false,
                },
                {
                    key: 'skip',
                    label: this.$tc('sw-first-run-wizard.general.buttonSkip'),
                    position: 'right',
                    variant: null,
                    action: 'sw.first.run.wizard.index.store',
                    disabled: false,
                },
                {
                    key: 'next',
                    label: this.$tc('sw-first-run-wizard.general.buttonNext'),
                    position: 'right',
                    variant: 'primary',
                    action: this.testCredentials.bind(this),
                    disabled: false,
                },
            ];

            this.$emit('buttons-update', buttonConfig);
        },

        testCredentials() {
            const { shuweiId, password } = this;

            return this.firstRunWizardService.checkShuweiId({
                shuweiId,
                password,
            }).then(() => {
                this.accountError = false;

                this.$emit('frw-redirect', 'sw.first.run.wizard.index.shuwei.domain');

                return false;
            }).catch(() => {
                this.accountError = true;

                return true;
            });
        },
    },
};
