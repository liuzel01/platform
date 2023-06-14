/**
 * @package admin
 */
describe('directives/click-outside', () => {
    it('should register the directive', () => {
        expect(Shuwei.Directive.getByName('click-outside')).toBeDefined();
    });
});
