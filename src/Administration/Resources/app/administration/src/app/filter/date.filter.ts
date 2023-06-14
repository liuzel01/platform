/**
 * @package admin
 */

Shuwei.Filter.register('date', (value: string, options: Intl.DateTimeFormatOptions = {}): string => {
    if (!value) {
        return '';
    }

    return Shuwei.Utils.format.date(value, options);
});

/**
 * @deprecated tag:v6.6.0 - Will be private
 */
export default {};
