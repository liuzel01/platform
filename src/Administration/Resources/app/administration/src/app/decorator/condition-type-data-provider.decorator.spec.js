import 'src/app/decorator/condition-type-data-provider.decorator';
import RuleConditionService from 'src/app/service/rule-condition.service';

describe('entity-collection.data.ts', () => {
    beforeAll(async () => {
        Shuwei.Service().register('ruleConditionDataProviderService', () => {
            return new RuleConditionService();
        });
    });

    it('should register conditions with correct scope', async () => {
        const condition = Shuwei.Service('ruleConditionDataProviderService').getByType('language');

        expect(condition).toBeDefined();
        expect(condition.scopes).toEqual(['global']);
    });

    it('should add app script conditions', async () => {
        Shuwei.Service('ruleConditionDataProviderService').addScriptConditions([
            {
                id: 'bar',
                name: 'foo',
                group: 'misc',
                config: {},
            },
        ]);

        const condition = Shuwei.Service('ruleConditionDataProviderService').getByType('bar');

        expect(condition.component).toBe('sw-condition-script');
        expect(condition.type).toBe('scriptRule');
        expect(condition.label).toBe('foo');
    });
});
