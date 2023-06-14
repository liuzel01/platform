import template from './sw-ai-copilot-badge.html.twig';
import './sw-ai-copilot-badge.scss';

/**
 * @package admin
 * @private
 */
export default Shuwei.Component.wrapComponentConfig({
    template,

    props: {
        label: {
            type: Boolean,
            required: false,
            default: true,
        },
    },
});
