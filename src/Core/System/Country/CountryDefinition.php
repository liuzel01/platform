<?php declare(strict_types=1);

namespace Shuwei\Core\System\Country;

use Shuwei\Core\Defaults;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\Country\Aggregate\CountryState\CountryStateDefinition;
use Shuwei\Core\System\Country\Aggregate\CountryTranslation\CountryTranslationDefinition;
use Shuwei\Core\System\Currency\Aggregate\CurrencyCountryRounding\CurrencyCountryRoundingDefinition;

#[Package('system-settings')]
class CountryDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'country';

    final public const TYPE_CUSTOMER_TAX_FREE = 'customer-tax-free';

    final public const TYPE_COMPANY_TAX_FREE = 'company-tax-free';

    final public const DEFAULT_ADDRESS_FORMAT = [
        ['address/company', 'symbol/dash', 'address/department'],
        ['address/first_name', 'address/last_name'],
        ['address/street'],
        ['address/zipcode', 'address/city'],
        ['address/country'],
    ];

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return CountryCollection::class;
    }

    public function getEntityClass(): string
    {
        return CountryEntity::class;
    }

    public function getDefaults(): array
    {
        $defaultTax = [
            'enabled' => false,
            'currencyId' => Defaults::CURRENCY,
            'amount' => 0,
        ];

        return [
            'vatIdRequired' => false,
            'postalCodeRequired' => false,
            'checkPostalCodePattern' => false,
            'checkAdvancedPostalCodePattern' => false,
            'customerTax' => $defaultTax,
            'companyTax' => $defaultTax,
        ];
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new PrimaryKey(), new Required()),

            (new TranslatedField('name'))->addFlags(new ApiAware(), new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING)),
            (new StringField('iso', 'iso'))->addFlags(new ApiAware(), new SearchRanking(SearchRanking::MIDDLE_SEARCH_RANKING)),
            (new IntField('position', 'position'))->addFlags(new ApiAware()),
            (new BoolField('active', 'active'))->addFlags(new ApiAware()),
            (new StringField('iso3', 'iso3'))->addFlags(new ApiAware(), new SearchRanking(SearchRanking::MIDDLE_SEARCH_RANKING)),
            (new BoolField('display_state_in_registration', 'displayStateInRegistration'))->addFlags(new ApiAware()),
            (new BoolField('force_state_in_registration', 'forceStateInRegistration'))->addFlags(new ApiAware()),
            (new BoolField('check_vat_id_pattern', 'checkVatIdPattern'))->addFlags(new ApiAware()),
            (new BoolField('vat_id_required', 'vatIdRequired'))->addFlags(new ApiAware()),
            (new StringField('vat_id_pattern', 'vatIdPattern'))->addFlags(new ApiAware()),
            (new TranslatedField('customFields'))->addFlags(new ApiAware()),
            (new BoolField('postal_code_required', 'postalCodeRequired'))->addFlags(new ApiAware()),
            (new BoolField('check_postal_code_pattern', 'checkPostalCodePattern'))->addFlags(new ApiAware()),
            (new BoolField('check_advanced_postal_code_pattern', 'checkAdvancedPostalCodePattern'))->addFlags(new ApiAware()),
            (new StringField('advanced_postal_code_pattern', 'advancedPostalCodePattern'))->addFlags(new ApiAware()),
            (new TranslatedField('addressFormat'))->addFlags(new ApiAware()),
            (new StringField('default_postal_code_pattern', 'defaultPostalCodePattern'))->addFlags(new ApiAware()),

            (new OneToManyAssociationField('states', CountryStateDefinition::class, 'country_id', 'id'))
                ->addFlags(new ApiAware(), new CascadeDelete()),

            (new TranslationsAssociationField(CountryTranslationDefinition::class, 'country_id'))
                ->addFlags(new ApiAware(), new Required()),

            (new OneToManyAssociationField('currencyCountryRoundings', CurrencyCountryRoundingDefinition::class, 'country_id'))
                ->addFlags(new CascadeDelete()),
        ]);
    }
}
