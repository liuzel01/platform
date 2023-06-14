/**
 * @package admin
 */

Shuwei.Filter.register('asset', (value: string) => {
    if (!value) {
        return '';
    }

    // Asset path already stars with an slash. Double slashes does not work on external storage like s3
    if (value[0] === '/') {
        value = value.substr(1);
    }

    const assetsPath = Shuwei.Context.api.assetsPath || '';

    return `${assetsPath}${value}`;
});

/**
 * @deprecated tag:v6.6.0 - Will be private
 */
export {};

