/**
 * @package admin
 */

/* @private */
export {};

/**
 * @deprecated tag:v6.6.0 - Will be private
 */
Shuwei.Mixin.register('sw-inline-snippet', {
    computed: {
        swInlineSnippetLocale(): string {
            return Shuwei.State.get('session').currentLocale as unknown as string;
        },
        swInlineSnippetFallbackLocale(): string {
            return Shuwei.Context.app.fallbackLocale as unknown as string;
        },
    },

    methods: {
        getInlineSnippet(value: {
            [key: string]: string;
        }) {
            if (Shuwei.Utils.types.isEmpty(value)) {
                return '';
            }
            if (value[this.swInlineSnippetLocale]) {
                return value[this.swInlineSnippetLocale];
            }
            if (value[this.swInlineSnippetFallbackLocale]) {
                return value[this.swInlineSnippetFallbackLocale];
            }
            if (Shuwei.Utils.types.isObject(value)) {
                const locale = Object.keys(value).find((key) => {
                    return value[key] !== '';
                });

                if (locale !== undefined) {
                    return value[locale];
                }
            }

            return value;
        },
    },
});
