/**
 * @package admin
 */

/**
 * @deprecated tag:v6.6.0 - Will be private
 */
Shuwei.Filter.register('fileSize', (value: number, locale: string) => {
    if (!value) {
        return '';
    }

    return Shuwei.Utils.format.fileSize(value, locale);
});

/* @private */
export {};
