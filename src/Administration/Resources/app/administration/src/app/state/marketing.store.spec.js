import ShuweiDiscountCampaignService from 'src/app/service/discount-campaign.service';
import marketingStore from 'src/app/state/marketing.store';

jest.useFakeTimers('modern');

describe('src/app/state/marketing.store', () => {
    beforeAll(() => {
        Shuwei.Service().register('shuweiDiscountCampaignService', () => {
            return new ShuweiDiscountCampaignService();
        });
    });

    beforeEach(() => {
        if (Shuwei.State.get('marketing')) {
            Shuwei.State.unregisterModule('marketing');
        }
        Shuwei.State.registerModule('marketing', marketingStore);

        Shuwei.State.get('marketing').campaign = marketingStore.state().campaign;
        jest.setSystemTime(new Date('2000-01-31').getTime());
    });

    it('should set a new campaign', async () => {
        Shuwei.State.commit('marketing/setCampaign', {
            name: 'Example campaign',
        });

        expect(Shuwei.State.get('marketing').campaign).toEqual({
            name: 'Example campaign',
        });
    });

    it('should return the active campaign when times match', async () => {
        // set date in active campaign time
        jest.setSystemTime(new Date('2005-08-17').getTime());

        Shuwei.State.commit('marketing/setCampaign', {
            name: 'Active campaign',
            startDate: '2005-08-15T15:52:01',
            endDate: '2005-08-20T15:52:01',
        });

        const activeCampaign = Shuwei.State.getters['marketing/getActiveCampaign'];
        expect(activeCampaign?.name).toBe('Active campaign');
    });

    it('should return null when times does not match', async () => {
        // set date outside the active campaign time
        jest.setSystemTime(new Date('2005-08-21').getTime());

        Shuwei.State.commit('marketing/setCampaign', {
            name: 'Inactive campaign',
            startDate: '2005-08-15T15:52:01',
            endDate: '2005-08-20T15:52:01',
        });

        const activeCampaign = Shuwei.State.getters['marketing/getActiveCampaign'];
        expect(activeCampaign).toBeNull();
    });

    it('should return the correct component for the store banner when time match', async () => {
        // set date in active campaign time
        jest.setSystemTime(new Date('2005-08-17').getTime());

        const storeBanner = {
            background: {
                color: '#ffffff',
                image: 'http://www.company.org/cum/sonoras',
                position: 'string',
            },
            content: {
                textColor: '#000000',
                headline: {
                    'en-US': 'string (max 40 Zeichen)',
                    'zh-CN': 'string (max 40 characters)',
                },
                description: {
                    'en-US': 'string (max 90 Zeichen)',
                    'zh-CN': 'string (max 90 characters)',
                },
                cta: {
                    category: 'CategoryXY',
                    'en-US': 'string (max 40 Zeichen)',
                    'zh-CN': 'string (max 40 characters)',
                },
            },
        };

        Shuwei.State.commit('marketing/setCampaign', {
            name: 'Active campaign',
            startDate: '2005-08-15T15:52:01',
            endDate: '2005-08-20T15:52:01',
            components: { storeBanner: storeBanner },
        });

        const storeComponent = Shuwei.State.getters['marketing/getActiveCampaignDataForComponent']('storeBanner');
        expect(storeComponent).toEqual({
            campaignName: 'Active campaign',
            component: storeBanner,
        });
    });

    it('should return null for the store banner when time does not match', async () => {
        // set date in active campaign time
        jest.setSystemTime(new Date('2005-08-21').getTime());

        const storeBanner = {
            background: {
                color: '#ffffff',
                image: 'http://www.company.org/cum/sonoras',
                position: 'string',
            },
            content: {
                textColor: '#000000',
                headline: {
                    'en-US': 'string (max 40 Zeichen)',
                    'zh-CN': 'string (max 40 characters)',
                },
                description: {
                    'en-US': 'string (max 90 Zeichen)',
                    'zh-CN': 'string (max 90 characters)',
                },
                cta: {
                    category: 'CategoryXY',
                    'en-US': 'string (max 40 Zeichen)',
                    'zh-CN': 'string (max 40 characters)',
                },
            },
        };

        Shuwei.State.commit('marketing/setCampaign', {
            name: 'Active campaign',
            startDate: '2005-08-15T15:52:01',
            endDate: '2005-08-20T15:52:01',
            components: {
                storeBanner: storeBanner,
            },
        });

        const storeComponent = Shuwei.State.getters['marketing/getActiveCampaignDataForComponent']('storeBanner');
        expect(storeComponent).toEqual({
            campaignName: undefined,
            component: null,
        });
    });
});
