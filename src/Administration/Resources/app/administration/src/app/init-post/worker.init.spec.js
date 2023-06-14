import WorkerNotificationFactory from 'src/core/factory/worker-notification.factory';
import initializeWorker from './worker.init';
import contextStore from '../state/context.store';

describe('src/app/init-post/worker.init.ts', () => {
    let loggedIn = false;
    let config = {};
    let loginListeners = [];

    beforeAll(() => {
        Shuwei.Service().register('loginService', () => {
            return {
                isLoggedIn: () => {
                    return loggedIn;
                },

                addOnLoginListener: (listener) => {
                    loginListeners.push(listener);
                },
                getBearerAuthentication: () => {
                    return 'jest';
                },
                refreshToken: () => {
                    return Promise.resolve();
                },

                addOnTokenChangedListener: () => {},
                addOnLogoutListener: () => {},
            };
        });

        Shuwei.Service().register('configService', () => {
            return {
                getConfig: () => {
                    return Promise.resolve(config);
                },
            };
        });
    });

    beforeEach(() => {
        const registry = WorkerNotificationFactory.getRegistry();
        registry.clear();

        WorkerNotificationFactory.resetHelper();

        if (Shuwei.State.get('context')) {
            Shuwei.State.unregisterModule('context');
        }

        Shuwei.State.registerModule('context', contextStore);
    });

    afterEach(() => {
        loggedIn = false;
        config = {};
        loginListeners = [];
        Shuwei.State.unregisterModule('context');
    });

    it('should not initialize if not logged in', () => {
        loggedIn = false;

        initializeWorker();

        expect(loginListeners).toHaveLength(1);
    });

    it.each([
        'Shuwei\\Core\\Framework\\DataAbstractionLayer\\Indexing\\MessageQueue\\IndexerMessage',
        'Shuwei\\Storefront\\Framework\\Cache\\CacheWarmer\\WarmUpMessage',
        'Shuwei\\Elasticsearch\\Framework\\Indexing\\IndexingMessage',
        'Shuwei\\Core\\Content\\Media\\Message\\GenerateThumbnailsMessage',
        'Shuwei\\Core\\Checkout\\Promotion\\DataAbstractionLayer\\PromotionIndexingMessage',
        'Shuwei\\Core\\Content\\ProductStream\\DataAbstractionLayer\\ProductStreamIndexingMessage',
        'Shuwei\\Core\\Content\\Category\\DataAbstractionLayer\\CategoryIndexingMessage',
        'Shuwei\\Core\\Content\\Media\\DataAbstractionLayer\\MediaIndexingMessage',
        'Shuwei\\Core\\System\\SalesChannel\\DataAbstractionLayer\\SalesChannelIndexingMessage',
        'Shuwei\\Core\\Content\\Rule\\DataAbstractionLayer\\RuleIndexingMessage',
        'Shuwei\\Core\\Content\\Product\\DataAbstractionLayer\\ProductIndexingMessage',
        'Shuwei\\Elasticsearch\\Framework\\Indexing\\ElasticsearchIndexingMessage',
        'Shuwei\\Core\\Content\\ImportExport\\Message\\ImportExportMessage',
        'Shuwei\\Core\\Content\\Flow\\Indexing\\FlowIndexingMessage',
        'Shuwei\\Core\\Content\\Newsletter\\DataAbstractionLayer\\NewsletterRecipientIndexingMessage',
    ])('should register thumbnail middleware "%s"', async (name) => {
        loggedIn = true;

        config = {
            adminWorker: {
                enableQueueStatsWorker: false,
            },
        };

        initializeWorker();
        const helper = WorkerNotificationFactory.initialize();

        const createMock = jest.fn(() => {
            return Promise.resolve('jest-id');
        });

        helper.go({
            queue: [
                { name, size: 1 },
            ],
            $root: {
                $tc: (msg) => msg,
            },
            notification: {
                create: createMock,
            },
        });

        await flushPromises();

        expect(loginListeners).toHaveLength(0);
        expect(createMock).toHaveBeenCalledTimes(1);
    });

    it('should update thumbnail middleware notifications', async () => {
        loggedIn = true;

        config = {
            adminWorker: {
                enableQueueStatsWorker: false,
            },
        };

        initializeWorker();
        const helper = WorkerNotificationFactory.initialize();

        const createMock = jest.fn(() => {
            return Promise.resolve('jest-id');
        });

        const updateMock = jest.fn(() => {
            return Promise.resolve();
        });

        // First run should create notification
        helper.go({
            queue: [
                { name: 'Shuwei\\Core\\Framework\\DataAbstractionLayer\\Indexing\\MessageQueue\\IndexerMessage', size: 1 },
            ],
            $root: {
                $tc: (msg) => msg,
            },
            notification: {
                create: createMock,
                update: updateMock,
            },
        });
        await flushPromises();
        expect(createMock).toHaveBeenCalledTimes(1);

        // Second run should update notification
        helper.go({
            queue: [
                { name: 'Shuwei\\Core\\Framework\\DataAbstractionLayer\\Indexing\\MessageQueue\\IndexerMessage', size: 0 },
            ],
            $root: {
                $t: (msg) => msg,
                $tc: (msg) => msg,
            },
            notification: {
                create: createMock,
                update: updateMock,
            },
        });
        await flushPromises();
        expect(updateMock).toHaveBeenCalledTimes(1);
    });

    it('should update config if logged in', async () => {
        loggedIn = true;

        config = {
            version: 'jest',
            adminWorker: {
                enableQueueStatsWorker: false,
            },
        };

        initializeWorker();
        await flushPromises();

        expect(loginListeners).toHaveLength(0);
        expect(Shuwei.State.get('context').app.config.version).toBe('jest');
    });
});
