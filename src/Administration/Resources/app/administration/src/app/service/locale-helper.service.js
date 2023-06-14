/**
 * @package admin
 *
 * @deprecated tag:v6.6.0 - Will be private
 * @memberOf module:core/service/locale
 * @constructor
 * @method createShortcutService
 * @returns {Object}
 */
export default class LocaleHelperService {
    _localeRepository;

    _localeFactory;

    _snippetService;

    _Shuwei;

    constructor({ Shuwei, localeRepository, snippetService, localeFactory }) {
        this._Shuwei = Shuwei;
        this._snippetService = snippetService;
        this._localeFactory = localeFactory;
        this._localeRepository = localeRepository;
    }

    async setLocaleWithId(localeId) {
        const { code } = await this._localeRepository.get(localeId, this._Shuwei.Context.api);

        await this.setLocaleWithCode(code);
    }

    async setLocaleWithCode(localeCode) {
        await this._snippetService.getSnippets(this._localeFactory, localeCode);
        await this._Shuwei.State.dispatch('setAdminLocale', localeCode);
    }
}

