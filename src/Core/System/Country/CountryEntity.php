<?php declare(strict_types=1);

namespace Shuwei\Core\System\Country;

use Shuwei\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressCollection;
use Shuwei\Core\Checkout\Order\Aggregate\OrderAddress\OrderAddressCollection;
use Shuwei\Core\Framework\DataAbstractionLayer\Entity;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shuwei\Core\Framework\DataAbstractionLayer\TaxFreeConfig;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\Country\Aggregate\CountryState\CountryStateCollection;
use Shuwei\Core\System\Country\Aggregate\CountryTranslation\CountryTranslationCollection;
use Shuwei\Core\System\Currency\Aggregate\CurrencyCountryRounding\CurrencyCountryRoundingCollection;
use Frontend\Website\WebsiteCollection;
use Shuwei\Core\System\Tax\Aggregate\TaxRule\TaxRuleCollection;

#[Package('system-settings')]
class CountryEntity extends Entity
{
    use EntityCustomFieldsTrait;
    use EntityIdTrait;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $iso;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var bool
     */
    protected $active;

    /**
     * @var bool
     */
    protected $shippingAvailable;

    /**
     * @var string|null
     */
    protected $iso3;

    /**
     * @var bool
     */
    protected $displayStateInRegistration;

    /**
     * @var bool
     */
    protected $forceStateInRegistration;

    /**
     * @var bool
     */
    protected $checkVatIdPattern;

    /**
     * @var string|null
     */
    protected $vatIdPattern;

    /**
     * @var bool|null
     */
    protected $vatIdRequired;

    protected TaxFreeConfig $customerTax;

    protected TaxFreeConfig $companyTax;

    /**
     * @var CountryStateCollection|null
     */
    protected $states;

    /**
     * @var CountryTranslationCollection|null
     */
    protected $translations;

    /**
     * @var OrderAddressCollection|null
     */
    protected $orderAddresses;

    /**
     * @var CustomerAddressCollection|null
     */
    protected $customerAddresses;

    /**
     * @var WebsiteCollection|null
     */
    protected $websiteDefaultAssignments;

    /**
     * @var WebsiteCollection|null
     */
    protected $websites;

    /**
     * @var TaxRuleCollection|null
     */
    protected $taxRules;

    /**
     * @var CurrencyCountryRoundingCollection|null
     */
    protected $currencyCountryRoundings;

    protected bool $postalCodeRequired;

    protected bool $checkPostalCodePattern;

    protected bool $checkAdvancedPostalCodePattern;

    protected ?string $advancedPostalCodePattern = null;

    protected ?string $defaultPostalCodePattern = null;

    /**
     * @var array<array<string, array<string, string>>>
     */
    protected array $addressFormat;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getIso(): ?string
    {
        return $this->iso;
    }

    public function setIso(?string $iso): void
    {
        $this->iso = $iso;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getShippingAvailable(): bool
    {
        return $this->shippingAvailable;
    }

    public function setShippingAvailable(bool $shippingAvailable): void
    {
        $this->shippingAvailable = $shippingAvailable;
    }

    public function getIso3(): ?string
    {
        return $this->iso3;
    }

    public function setIso3(?string $iso3): void
    {
        $this->iso3 = $iso3;
    }

    public function getDisplayStateInRegistration(): bool
    {
        return $this->displayStateInRegistration;
    }

    public function setDisplayStateInRegistration(bool $displayStateInRegistration): void
    {
        $this->displayStateInRegistration = $displayStateInRegistration;
    }

    public function getForceStateInRegistration(): bool
    {
        return $this->forceStateInRegistration;
    }

    public function setForceStateInRegistration(bool $forceStateInRegistration): void
    {
        $this->forceStateInRegistration = $forceStateInRegistration;
    }

    public function getCheckVatIdPattern(): bool
    {
        return $this->checkVatIdPattern;
    }

    public function setCheckVatIdPattern(bool $checkVatIdPattern): void
    {
        $this->checkVatIdPattern = $checkVatIdPattern;
    }

    public function getVatIdPattern(): ?string
    {
        return $this->vatIdPattern;
    }

    public function setVatIdPattern(?string $vatIdPattern): void
    {
        $this->vatIdPattern = $vatIdPattern;
    }

    public function getStates(): ?CountryStateCollection
    {
        return $this->states;
    }

    public function setStates(CountryStateCollection $states): void
    {
        $this->states = $states;
    }

    public function getTranslations(): ?CountryTranslationCollection
    {
        return $this->translations;
    }

    public function setTranslations(CountryTranslationCollection $translations): void
    {
        $this->translations = $translations;
    }

    public function getOrderAddresses(): ?OrderAddressCollection
    {
        return $this->orderAddresses;
    }

    public function setOrderAddresses(OrderAddressCollection $orderAddresses): void
    {
        $this->orderAddresses = $orderAddresses;
    }

    public function getCustomerAddresses(): ?CustomerAddressCollection
    {
        return $this->customerAddresses;
    }

    public function setCustomerAddresses(CustomerAddressCollection $customerAddresses): void
    {
        $this->customerAddresses = $customerAddresses;
    }

    public function getWebsiteDefaultAssignments(): ?WebsiteCollection
    {
        return $this->websiteDefaultAssignments;
    }

    public function setWebsiteDefaultAssignments(WebsiteCollection $websiteDefaultAssignments): void
    {
        $this->websiteDefaultAssignments = $websiteDefaultAssignments;
    }

    public function getWebsites(): ?WebsiteCollection
    {
        return $this->websites;
    }

    public function setWebsites(WebsiteCollection $websites): void
    {
        $this->websites = $websites;
    }

    public function getTaxRules(): ?TaxRuleCollection
    {
        return $this->taxRules;
    }

    public function setTaxRules(TaxRuleCollection $taxRules): void
    {
        $this->taxRules = $taxRules;
    }

    public function getCurrencyCountryRoundings(): ?CurrencyCountryRoundingCollection
    {
        return $this->currencyCountryRoundings;
    }

    public function setCurrencyCountryRoundings(CurrencyCountryRoundingCollection $currencyCountryRoundings): void
    {
        $this->currencyCountryRoundings = $currencyCountryRoundings;
    }

    public function getVatIdRequired(): bool
    {
        return (bool) $this->vatIdRequired;
    }

    public function setVatIdRequired(bool $vatIdRequired): void
    {
        $this->vatIdRequired = $vatIdRequired;
    }

    public function getCustomerTax(): TaxFreeConfig
    {
        return $this->customerTax;
    }

    public function setCustomerTax(TaxFreeConfig $customerTax): void
    {
        $this->customerTax = $customerTax;
    }

    public function getCompanyTax(): TaxFreeConfig
    {
        return $this->companyTax;
    }

    public function setCompanyTax(TaxFreeConfig $companyTax): void
    {
        $this->companyTax = $companyTax;
    }

    public function getPostalCodeRequired(): bool
    {
        return $this->postalCodeRequired;
    }

    public function setPostalCodeRequired(bool $postalCodeRequired): void
    {
        $this->postalCodeRequired = $postalCodeRequired;
    }

    public function getCheckPostalCodePattern(): bool
    {
        return $this->checkPostalCodePattern;
    }

    public function setCheckPostalCodePattern(bool $checkPostalCodePattern): void
    {
        $this->checkPostalCodePattern = $checkPostalCodePattern;
    }

    public function getCheckAdvancedPostalCodePattern(): bool
    {
        return $this->checkAdvancedPostalCodePattern;
    }

    public function setCheckAdvancedPostalCodePattern(bool $checkAdvancedPostalCodePattern): void
    {
        $this->checkAdvancedPostalCodePattern = $checkAdvancedPostalCodePattern;
    }

    public function getAdvancedPostalCodePattern(): ?string
    {
        return $this->advancedPostalCodePattern;
    }

    public function setAdvancedPostalCodePattern(?string $advancedPostalCodePattern): void
    {
        $this->advancedPostalCodePattern = $advancedPostalCodePattern;
    }

    /**
     * @return array<array<string, array<string, string>>>
     */
    public function getAddressFormat(): array
    {
        return $this->addressFormat;
    }

    /**
     * @param array<array<string, array<string, string>>> $addressFormat
     */
    public function setAddressFormat(array $addressFormat): void
    {
        $this->addressFormat = $addressFormat;
    }

    public function setDefaultPostalCodePattern(?string $pattern): void
    {
        $this->defaultPostalCodePattern = $pattern;
    }

    public function getDefaultPostalCodePattern(): ?string
    {
        return $this->defaultPostalCodePattern;
    }
}
