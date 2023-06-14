import ShuweiError from 'src/core/data/ShuweiError';

const { string } = Shuwei.Utils;

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
interface ApiError {
    code: string;
    title: string;
    detail: string;
    meta: {
        parameters: object;
    };
    status: string;
    source?: {
        pointer?: string;
    };
}

/**
 * @package system-settings
 *
 * @deprecated tag:v6.6.0 - Will be private
 */
export default class ErrorResolverSystemConfig {
    public static ENTITY_NAME = 'SYSTEM_CONFIG';

    private readonly merge;

    constructor() {
        this.merge = Shuwei.Utils.object.merge;
    }

    public handleWriteErrors(errors?: ApiError[]) {
        if (!errors) {
            throw new Error('[error-resolver] handleWriteError was called without errors');
        }

        const writeErrors = this.reduceErrorsByWriteIndex(errors);

        if (writeErrors.systemError.length > 0) {
            this.addSystemErrors(writeErrors.systemError);
        }

        this.handleErrors(writeErrors.apiError);
    }

    public cleanWriteErrors() {
        void Shuwei.State.dispatch('error/resetApiErrors');
    }

    private reduceErrorsByWriteIndex(errors: ApiError[]) {
        const writeErrors: {
            systemError: ShuweiError[],
            apiError: {
                [key: string]: ShuweiError
            },
        } = {
            systemError: [],
            apiError: {},
        };

        errors.forEach((current) => {
            if (!current.source || !current.source.pointer) {
                const systemError = new ShuweiError({
                    code: current.code,
                    meta: current.meta,
                    detail: current.detail,
                    status: current.status,
                });
                writeErrors.systemError.push(systemError);

                return;
            }

            const segments = current.source.pointer.split('/');

            // remove first empty element in list
            if (segments[0] === '') {
                segments.shift();
            }

            const denormalized = {};
            const lastIndex = segments.length - 1;

            segments.reduce((pointer: {[key: string]: Partial<ShuweiError> }, segment, index) => {
                // skip translations
                if (segment === 'translations' || segments[index - 1] === 'translations') {
                    return pointer;
                }

                if (index === lastIndex) {
                    pointer[segment] = new ShuweiError(current);
                } else {
                    pointer[segment] = {};
                }

                return pointer[segment];
            }, denormalized);

            writeErrors.apiError = this.merge(writeErrors.apiError, denormalized);
        });

        return writeErrors;
    }

    private addSystemErrors(errors: ShuweiError[]) {
        errors.forEach((error) => {
            void Shuwei.State.dispatch('error/addSystemError', error);
        });
    }

    private handleErrors(errors: {[key: string]: ShuweiError}) {
        Object.keys(errors).forEach((key: string) => {
            void Shuwei.State.dispatch('error/addApiError', {
                expression: this.getErrorPath(key),
                error: errors[key],
            });
        });
    }

    private getErrorPath(key: string) {
        key = string.camelCase(key);

        return `${ErrorResolverSystemConfig.ENTITY_NAME}.${key}`;
    }
}
