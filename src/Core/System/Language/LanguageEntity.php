<?php declare(strict_types=1);

namespace Shuwei\Core\System\Language;

use Shuwei\Core\Checkout\Customer\Aggregate\CustomerGroupTranslation\CustomerGroupTranslationCollection;
use Shuwei\Core\Checkout\Customer\CustomerCollection;
use Shuwei\Core\Checkout\Document\Aggregate\DocumentTypeTranslation\DocumentTypeTranslationCollection;
use Shuwei\Core\Checkout\Order\OrderCollection;
use Shuwei\Core\Checkout\Payment\Aggregate\PaymentMethodTranslation\PaymentMethodTranslationCollection;
use Shuwei\Core\Checkout\Promotion\Aggregate\PromotionTranslation\PromotionTranslationCollection;
use Shuwei\Core\Checkout\Shipping\Aggregate\ShippingMethodTranslation\ShippingMethodTranslationCollection;
use Shuwei\Core\Content\Category\Aggregate\CategoryTranslation\CategoryTranslationCollection;
use Shuwei\Core\Content\Cms\Aggregate\CmsPageTranslation\CmsPageTranslationEntity;
use Shuwei\Core\Content\Cms\Aggregate\CmsSlotTranslation\CmsSlotTranslationEntity;
use Shuwei\Core\Content\ImportExport\ImportExportProfileTranslationCollection;
use Shuwei\Core\Content\LandingPage\Aggregate\LandingPageTranslation\LandingPageTranslationCollection;
use Shuwei\Core\Content\MailTemplate\Aggregate\MailHeaderFooter\MailHeaderFooterCollection;
use Shuwei\Core\Content\MailTemplate\Aggregate\MailTemplateType\MailTemplateTypeDefinition;
use Shuwei\Core\Content\MailTemplate\MailTemplateCollection;
use Shuwei\Core\Content\Media\Aggregate\MediaTranslation\MediaTranslationCollection;
use Shuwei\Core\Content\Newsletter\Aggregate\NewsletterRecipient\NewsletterRecipientCollection;
use Shuwei\Core\Content\Product\Aggregate\ProductCrossSellingTranslation\ProductCrossSellingTranslationCollection;
use Shuwei\Core\Content\Product\Aggregate\ProductFeatureSetTranslation\ProductFeatureSetTranslationCollection;
use Shuwei\Core\Content\Product\Aggregate\ProductKeywordDictionary\ProductKeywordDictionaryCollection;
use Shuwei\Core\Content\Product\Aggregate\ProductManufacturerTranslation\ProductManufacturerTranslationCollection;
use Shuwei\Core\Content\Product\Aggregate\ProductReview\ProductReviewCollection;
use Shuwei\Core\Content\Product\Aggregate\ProductSearchConfig\ProductSearchConfigEntity;
use Shuwei\Core\Content\Product\Aggregate\ProductSearchKeyword\ProductSearchKeywordCollection;
use Shuwei\Core\Content\Product\Aggregate\ProductTranslation\ProductTranslationCollection;
use Shuwei\Core\Content\Product\Website\Sorting\ProductSortingTranslationCollection;
use Shuwei\Core\Content\ProductStream\Aggregate\ProductStreamTranslation\ProductStreamTranslationCollection;
use Shuwei\Core\Content\Property\Aggregate\PropertyGroupOptionTranslation\PropertyGroupOptionTranslationCollection;
use Shuwei\Core\Content\Property\Aggregate\PropertyGroupTranslation\PropertyGroupTranslationCollection;
use Shuwei\Core\Content\Seo\SeoUrl\SeoUrlCollection;
use Shuwei\Core\Framework\App\Aggregate\ActionButtonTranslation\ActionButtonTranslationCollection;
use Shuwei\Core\Framework\App\Aggregate\AppScriptConditionTranslation\AppScriptConditionTranslationCollection;
use Shuwei\Core\Framework\App\Aggregate\AppTranslation\AppTranslationCollection;
use Shuwei\Core\Framework\App\Aggregate\CmsBlockTranslation\AppCmsBlockTranslationCollection;
use Shuwei\Core\Framework\App\Aggregate\FlowActionTranslation\AppFlowActionTranslationCollection;
use Shuwei\Core\Framework\DataAbstractionLayer\Entity;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Plugin\Aggregate\PluginTranslation\PluginTranslationCollection;
use Shuwei\Core\Framework\Struct\Collection;
use Shuwei\Core\System\Country\Aggregate\CountryStateTranslation\CountryStateTranslationCollection;
use Shuwei\Core\System\Country\Aggregate\CountryTranslation\CountryTranslationCollection;
use Shuwei\Core\System\Currency\Aggregate\CurrencyTranslation\CurrencyTranslationCollection;
use Shuwei\Core\System\DeliveryTime\DeliveryTimeCollection;
use Shuwei\Core\System\Locale\Aggregate\LocaleTranslation\LocaleTranslationCollection;
use Shuwei\Core\System\Locale\LocaleEntity;
use Shuwei\Core\System\NumberRange\Aggregate\NumberRangeTranslation\NumberRangeTranslationCollection;
use Shuwei\Core\System\NumberRange\Aggregate\NumberRangeTypeTranslation\NumberRangeTypeTranslationCollection;
use Shuwei\Core\System\Website\Aggregate\WebsiteDomain\WebsiteDomainCollection;
use Shuwei\Core\System\Website\Aggregate\WebsiteTranslation\WebsiteTranslationCollection;
use Shuwei\Core\System\Website\Aggregate\WebsiteTypeTranslation\WebsiteTypeTranslationCollection;
use Shuwei\Core\System\Website\WebsiteCollection;
use Shuwei\Core\System\Salutation\Aggregate\SalutationTranslation\SalutationTranslationCollection;
use Shuwei\Core\System\StateMachine\Aggregation\StateMachineState\StateMachineStateTranslationCollection;
use Shuwei\Core\System\StateMachine\StateMachineTranslationCollection;
use Shuwei\Core\System\Tax\Aggregate\TaxRuleTypeTranslation\TaxRuleTypeTranslationCollection;
use Shuwei\Core\System\TaxProvider\Aggregate\TaxProviderTranslation\TaxProviderTranslationCollection;
use Shuwei\Core\System\Unit\Aggregate\UnitTranslation\UnitTranslationCollection;

#[Package('system-settings')]
class LanguageEntity extends Entity
{
    use EntityCustomFieldsTrait;
    use EntityIdTrait;

    /**
     * @var string|null
     */
    protected $parentId;

    /**
     * @var string
     */
    protected $localeId;

    /**
     * @var string|null
     */
    protected $translationCodeId;

    /**
     * @var LocaleEntity|null
     */
    protected $translationCode;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var LocaleEntity|null
     */
    protected $locale;

    /**
     * @var LanguageEntity|null
     */
    protected $parent;

    /**
     * @var LanguageCollection|null
     */
    protected $children;

    /**
     * @var WebsiteCollection|null
     */
    protected $websites;

    /**
     * @var CustomerCollection|null
     */
    protected $customers;

    /**
     * @var WebsiteCollection|null
     */
    protected $websiteDefaultAssignments;

    /**
     * @var CategoryTranslationCollection|null
     */
    protected $categoryTranslations;

    /**
     * @var CountryStateTranslationCollection|null
     */
    protected $countryStateTranslations;

    /**
     * @var CountryTranslationCollection|null
     */
    protected $countryTranslations;

    /**
     * @var CurrencyTranslationCollection|null
     */
    protected $currencyTranslations;

    /**
     * @var CustomerGroupTranslationCollection|null
     */
    protected $customerGroupTranslations;

    /**
     * @var LocaleTranslationCollection|null
     */
    protected $localeTranslations;

    /**
     * @var MediaTranslationCollection|null
     */
    protected $mediaTranslations;

    /**
     * @var PaymentMethodTranslationCollection|null
     */
    protected $paymentMethodTranslations;

    /**
     * @var ProductManufacturerTranslationCollection|null
     */
    protected $productManufacturerTranslations;

    /**
     * @var ProductTranslationCollection|null
     */
    protected $productTranslations;

    /**
     * @var ShippingMethodTranslationCollection|null
     */
    protected $shippingMethodTranslations;

    /**
     * @var UnitTranslationCollection|null
     */
    protected $unitTranslations;

    /**
     * @var PropertyGroupTranslationCollection|null
     */
    protected $propertyGroupTranslations;

    /**
     * @var PropertyGroupOptionTranslationCollection|null
     */
    protected $propertyGroupOptionTranslations;

    /**
     * @var WebsiteTranslationCollection|null
     */
    protected $websiteTranslations;

    /**
     * @var WebsiteTypeTranslationCollection|null
     */
    protected $websiteTypeTranslations;

    /**
     * @var SalutationTranslationCollection|null
     */
    protected $salutationTranslations;

    /**
     * @var WebsiteDomainCollection|null
     */
    protected $websiteDomains;

    /**
     * @var PluginTranslationCollection|null
     */
    protected $pluginTranslations;

    /**
     * @var ProductStreamTranslationCollection|null
     */
    protected $productStreamTranslations;

    /**
     * @var StateMachineTranslationCollection|null
     */
    protected $stateMachineTranslations;

    /**
     * @var StateMachineStateTranslationCollection|null
     */
    protected $stateMachineStateTranslations;

    /**
     * @var EntityCollection<CmsPageTranslationEntity>|null
     */
    protected $cmsPageTranslations;

    /**
     * @var EntityCollection<CmsSlotTranslationEntity>|null
     */
    protected $cmsSlotTranslations;

    /**
     * @var MailTemplateCollection|null
     */
    protected $mailTemplateTranslations;

    /**
     * @var MailHeaderFooterCollection|null
     */
    protected $mailHeaderFooterTranslations;

    /**
     * @var DocumentTypeTranslationCollection|null
     */
    protected $documentTypeTranslations;

    /**
     * @var DeliveryTimeCollection|null
     */
    protected $deliveryTimeTranslations;

    /**
     * @var NewsletterRecipientCollection|null
     */
    protected $newsletterRecipients;

    /**
     * @var OrderCollection|null
     */
    protected $orders;

    /**
     * @var NumberRangeTypeTranslationCollection|null
     */
    protected $numberRangeTypeTranslations;

    /**
     * @var ProductSearchKeywordCollection|null
     */
    protected $productSearchKeywords;

    /**
     * @var ProductKeywordDictionaryCollection|null
     */
    protected $productKeywordDictionaries;

    /**
     * @var MailTemplateTypeDefinition|null
     */
    protected $mailTemplateTypeTranslations;

    /**
     * @var PromotionTranslationCollection|null
     */
    protected $promotionTranslations;

    /**
     * @var NumberRangeTranslationCollection|null
     */
    protected $numberRangeTranslations;

    /**
     * @var ProductReviewCollection|null
     */
    protected $productReviews;

    /**
     * @var SeoUrlCollection|null
     */
    protected $seoUrlTranslations;

    /**
     * @var TaxRuleTypeTranslationCollection|null
     */
    protected $taxRuleTypeTranslations;

    /**
     * @var ProductCrossSellingTranslationCollection|null
     */
    protected $productCrossSellingTranslations;

    /**
     * @var ImportExportProfileTranslationCollection|null
     */
    protected $importExportProfileTranslations;

    /**
     * @var ProductFeatureSetTranslationCollection|null
     */
    protected $productFeatureSetTranslations;

    /**
     * @var AppTranslationCollection|null
     */
    protected $appTranslations;

    /**
     * @var ActionButtonTranslationCollection|null
     */
    protected $actionButtonTranslations;

    /**
     * @var ProductSortingTranslationCollection|null
     */
    protected $productSortingTranslations;

    /**
     * @var ProductSearchConfigEntity|null
     */
    protected $productSearchConfig;

    /**
     * @var LandingPageTranslationCollection|null
     */
    protected $landingPageTranslations;

    /**
     * @var AppCmsBlockTranslationCollection|null
     */
    protected $appCmsBlockTranslations;

    /**
     * @var AppScriptConditionTranslationCollection|null
     */
    protected $appScriptConditionTranslations;

    /**
     * @var AppFlowActionTranslationCollection|null
     */
    protected $appFlowActionTranslations;

    protected ?TaxProviderTranslationCollection $taxProviderTranslations = null;

    public function getMailHeaderFooterTranslations(): ?MailHeaderFooterCollection
    {
        return $this->mailHeaderFooterTranslations;
    }

    public function setMailHeaderFooterTranslations(MailHeaderFooterCollection $mailHeaderFooterTranslations): void
    {
        $this->mailHeaderFooterTranslations = $mailHeaderFooterTranslations;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function setParentId(?string $parentId): void
    {
        $this->parentId = $parentId;
    }

    public function getLocaleId(): string
    {
        return $this->localeId;
    }

    public function setLocaleId(string $localeId): void
    {
        $this->localeId = $localeId;
    }

    public function getTranslationCodeId(): ?string
    {
        return $this->translationCodeId;
    }

    public function setTranslationCodeId(?string $translationCodeId): void
    {
        $this->translationCodeId = $translationCodeId;
    }

    public function getTranslationCode(): ?LocaleEntity
    {
        return $this->translationCode;
    }

    public function setTranslationCode(?LocaleEntity $translationCode): void
    {
        $this->translationCode = $translationCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLocale(): ?LocaleEntity
    {
        return $this->locale;
    }

    public function setLocale(LocaleEntity $locale): void
    {
        $this->locale = $locale;
    }

    public function getParent(): ?LanguageEntity
    {
        return $this->parent;
    }

    public function setParent(LanguageEntity $parent): void
    {
        $this->parent = $parent;
    }

    public function getChildren(): ?LanguageCollection
    {
        return $this->children;
    }

    public function setChildren(LanguageCollection $children): void
    {
        $this->children = $children;
    }

    public function getCategoryTranslations(): ?CategoryTranslationCollection
    {
        return $this->categoryTranslations;
    }

    public function setCategoryTranslations(CategoryTranslationCollection $categoryTranslations): void
    {
        $this->categoryTranslations = $categoryTranslations;
    }

    public function getCountryStateTranslations(): ?CountryStateTranslationCollection
    {
        return $this->countryStateTranslations;
    }

    public function setCountryStateTranslations(CountryStateTranslationCollection $countryStateTranslations): void
    {
        $this->countryStateTranslations = $countryStateTranslations;
    }

    public function getCountryTranslations(): ?CountryTranslationCollection
    {
        return $this->countryTranslations;
    }

    public function setCountryTranslations(CountryTranslationCollection $countryTranslations): void
    {
        $this->countryTranslations = $countryTranslations;
    }

    public function getCurrencyTranslations(): ?CurrencyTranslationCollection
    {
        return $this->currencyTranslations;
    }

    public function setCurrencyTranslations(CurrencyTranslationCollection $currencyTranslations): void
    {
        $this->currencyTranslations = $currencyTranslations;
    }

    public function getCustomerGroupTranslations(): ?CustomerGroupTranslationCollection
    {
        return $this->customerGroupTranslations;
    }

    public function setCustomerGroupTranslations(CustomerGroupTranslationCollection $customerGroupTranslations): void
    {
        $this->customerGroupTranslations = $customerGroupTranslations;
    }

    public function getLocaleTranslations(): ?LocaleTranslationCollection
    {
        return $this->localeTranslations;
    }

    public function setLocaleTranslations(LocaleTranslationCollection $localeTranslations): void
    {
        $this->localeTranslations = $localeTranslations;
    }

    public function getMediaTranslations(): ?MediaTranslationCollection
    {
        return $this->mediaTranslations;
    }

    public function setMediaTranslations(MediaTranslationCollection $mediaTranslations): void
    {
        $this->mediaTranslations = $mediaTranslations;
    }

    public function getPaymentMethodTranslations(): ?PaymentMethodTranslationCollection
    {
        return $this->paymentMethodTranslations;
    }

    public function setPaymentMethodTranslations(PaymentMethodTranslationCollection $paymentMethodTranslations): void
    {
        $this->paymentMethodTranslations = $paymentMethodTranslations;
    }

    public function getProductManufacturerTranslations(): ?ProductManufacturerTranslationCollection
    {
        return $this->productManufacturerTranslations;
    }

    public function setProductManufacturerTranslations(ProductManufacturerTranslationCollection $productManufacturerTranslations): void
    {
        $this->productManufacturerTranslations = $productManufacturerTranslations;
    }

    public function getProductTranslations(): ?ProductTranslationCollection
    {
        return $this->productTranslations;
    }

    public function setProductTranslations(ProductTranslationCollection $productTranslations): void
    {
        $this->productTranslations = $productTranslations;
    }

    public function getShippingMethodTranslations(): ?ShippingMethodTranslationCollection
    {
        return $this->shippingMethodTranslations;
    }

    public function setShippingMethodTranslations(ShippingMethodTranslationCollection $shippingMethodTranslations): void
    {
        $this->shippingMethodTranslations = $shippingMethodTranslations;
    }

    public function getUnitTranslations(): ?UnitTranslationCollection
    {
        return $this->unitTranslations;
    }

    public function setUnitTranslations(UnitTranslationCollection $unitTranslations): void
    {
        $this->unitTranslations = $unitTranslations;
    }

    public function getWebsites(): ?WebsiteCollection
    {
        return $this->websites;
    }

    public function setWebsites(WebsiteCollection $websites): void
    {
        $this->websites = $websites;
    }

    public function getWebsiteDefaultAssignments(): ?WebsiteCollection
    {
        return $this->websiteDefaultAssignments;
    }

    public function getCustomers(): ?CustomerCollection
    {
        return $this->customers;
    }

    public function setCustomers(CustomerCollection $customers): void
    {
        $this->customers = $customers;
    }

    public function setWebsiteDefaultAssignments(WebsiteCollection $websiteDefaultAssignments): void
    {
        $this->websiteDefaultAssignments = $websiteDefaultAssignments;
    }

    public function getSalutationTranslations(): ?SalutationTranslationCollection
    {
        return $this->salutationTranslations;
    }

    public function setSalutationTranslations(SalutationTranslationCollection $salutationTranslations): void
    {
        $this->salutationTranslations = $salutationTranslations;
    }

    public function getPropertyGroupTranslations(): ?PropertyGroupTranslationCollection
    {
        return $this->propertyGroupTranslations;
    }

    public function setPropertyGroupTranslations(PropertyGroupTranslationCollection $propertyGroupTranslations): void
    {
        $this->propertyGroupTranslations = $propertyGroupTranslations;
    }

    public function getPropertyGroupOptionTranslations(): ?PropertyGroupOptionTranslationCollection
    {
        return $this->propertyGroupOptionTranslations;
    }

    public function setPropertyGroupOptionTranslations(PropertyGroupOptionTranslationCollection $propertyGroupOptionTranslationCollection): void
    {
        $this->propertyGroupOptionTranslations = $propertyGroupOptionTranslationCollection;
    }

    public function getWebsiteTranslations(): ?WebsiteTranslationCollection
    {
        return $this->websiteTranslations;
    }

    public function setWebsiteTranslations(WebsiteTranslationCollection $websiteTranslations): void
    {
        $this->websiteTranslations = $websiteTranslations;
    }

    public function getWebsiteTypeTranslations(): ?WebsiteTypeTranslationCollection
    {
        return $this->websiteTypeTranslations;
    }

    public function setWebsiteTypeTranslations(WebsiteTypeTranslationCollection $websiteTypeTranslations): void
    {
        $this->websiteTypeTranslations = $websiteTypeTranslations;
    }

    public function getWebsiteDomains(): ?WebsiteDomainCollection
    {
        return $this->websiteDomains;
    }

    public function setWebsiteDomains(WebsiteDomainCollection $websiteDomains): void
    {
        $this->websiteDomains = $websiteDomains;
    }

    public function getPluginTranslations(): ?PluginTranslationCollection
    {
        return $this->pluginTranslations;
    }

    public function setPluginTranslations(PluginTranslationCollection $pluginTranslations): void
    {
        $this->pluginTranslations = $pluginTranslations;
    }

    public function getProductStreamTranslations(): ?ProductStreamTranslationCollection
    {
        return $this->productStreamTranslations;
    }

    public function setProductStreamTranslations(ProductStreamTranslationCollection $productStreamTranslations): void
    {
        $this->productStreamTranslations = $productStreamTranslations;
    }

    /**
     * @return StateMachineTranslationCollection|null
     */
    public function getStateMachineTranslations(): ?Collection
    {
        return $this->stateMachineTranslations;
    }

    /**
     * @param StateMachineTranslationCollection $stateMachineTranslations
     */
    public function setStateMachineTranslations(Collection $stateMachineTranslations): void
    {
        $this->stateMachineTranslations = $stateMachineTranslations;
    }

    /**
     * @return StateMachineStateTranslationCollection|null
     */
    public function getStateMachineStateTranslations(): ?Collection
    {
        return $this->stateMachineStateTranslations;
    }

    /**
     * @param StateMachineStateTranslationCollection $stateMachineStateTranslations
     */
    public function setStateMachineStateTranslations(Collection $stateMachineStateTranslations): void
    {
        $this->stateMachineStateTranslations = $stateMachineStateTranslations;
    }

    /**
     * @return EntityCollection<CmsPageTranslationEntity>|null
     */
    public function getCmsPageTranslations(): ?Collection
    {
        return $this->cmsPageTranslations;
    }

    /**
     * @param EntityCollection<CmsPageTranslationEntity> $cmsPageTranslations
     */
    public function setCmsPageTranslations(Collection $cmsPageTranslations): void
    {
        $this->cmsPageTranslations = $cmsPageTranslations;
    }

    /**
     * @return EntityCollection<CmsSlotTranslationEntity>|null
     */
    public function getCmsSlotTranslations(): ?Collection
    {
        return $this->cmsSlotTranslations;
    }

    /**
     * @param EntityCollection<CmsSlotTranslationEntity> $cmsSlotTranslations
     */
    public function setCmsSlotTranslations(Collection $cmsSlotTranslations): void
    {
        $this->cmsSlotTranslations = $cmsSlotTranslations;
    }

    public function getMailTemplateTranslations(): ?MailTemplateCollection
    {
        return $this->mailTemplateTranslations;
    }

    public function setMailTemplateTranslations(MailTemplateCollection $mailTemplateTranslations): void
    {
        $this->mailTemplateTranslations = $mailTemplateTranslations;
    }

    public function getDocumentTypeTranslations(): ?DocumentTypeTranslationCollection
    {
        return $this->documentTypeTranslations;
    }

    public function setDocumentTypeTranslations(DocumentTypeTranslationCollection $documentTypeTranslations): void
    {
        $this->documentTypeTranslations = $documentTypeTranslations;
    }

    public function getDeliveryTimeTranslations(): ?DeliveryTimeCollection
    {
        return $this->deliveryTimeTranslations;
    }

    public function setDeliveryTimeTranslations(DeliveryTimeCollection $deliveryTimeTranslations): void
    {
        $this->deliveryTimeTranslations = $deliveryTimeTranslations;
    }

    public function getNewsletterRecipients(): ?NewsletterRecipientCollection
    {
        return $this->newsletterRecipients;
    }

    public function setNewsletterRecipients(NewsletterRecipientCollection $newsletterRecipients): void
    {
        $this->newsletterRecipients = $newsletterRecipients;
    }

    public function getOrders(): ?OrderCollection
    {
        return $this->orders;
    }

    public function setOrders(OrderCollection $orders): void
    {
        $this->orders = $orders;
    }

    public function getNumberRangeTypeTranslations(): ?NumberRangeTypeTranslationCollection
    {
        return $this->numberRangeTypeTranslations;
    }

    public function setNumberRangeTypeTranslations(NumberRangeTypeTranslationCollection $numberRangeTypeTranslations): void
    {
        $this->numberRangeTypeTranslations = $numberRangeTypeTranslations;
    }

    public function getMailTemplateTypeTranslations(): ?MailTemplateTypeDefinition
    {
        return $this->mailTemplateTypeTranslations;
    }

    public function setMailTemplateTypeTranslations(MailTemplateTypeDefinition $mailTemplateTypeTranslations): void
    {
        $this->mailTemplateTypeTranslations = $mailTemplateTypeTranslations;
    }

    public function getProductSearchKeywords(): ?ProductSearchKeywordCollection
    {
        return $this->productSearchKeywords;
    }

    public function setProductSearchKeywords(ProductSearchKeywordCollection $productSearchKeywords): void
    {
        $this->productSearchKeywords = $productSearchKeywords;
    }

    public function getProductKeywordDictionaries(): ?ProductKeywordDictionaryCollection
    {
        return $this->productKeywordDictionaries;
    }

    public function setProductKeywordDictionaries(ProductKeywordDictionaryCollection $productKeywordDictionaries): void
    {
        $this->productKeywordDictionaries = $productKeywordDictionaries;
    }

    public function getPromotionTranslations(): ?PromotionTranslationCollection
    {
        return $this->promotionTranslations;
    }

    public function setPromotionTranslations(PromotionTranslationCollection $promotionTranslations): void
    {
        $this->promotionTranslations = $promotionTranslations;
    }

    public function getNumberRangeTranslations(): ?NumberRangeTranslationCollection
    {
        return $this->numberRangeTranslations;
    }

    public function setNumberRangeTranslations(NumberRangeTranslationCollection $numberRangeTranslations): void
    {
        $this->numberRangeTranslations = $numberRangeTranslations;
    }

    public function getProductReviews(): ?ProductReviewCollection
    {
        return $this->productReviews;
    }

    public function setProductReviews(ProductReviewCollection $productReviews): void
    {
        $this->productReviews = $productReviews;
    }

    public function getSeoUrlTranslations(): ?SeoUrlCollection
    {
        return $this->seoUrlTranslations;
    }

    public function setSeoUrlTranslations(SeoUrlCollection $seoUrlTranslations): void
    {
        $this->seoUrlTranslations = $seoUrlTranslations;
    }

    public function getTaxRuleTypeTranslations(): ?TaxRuleTypeTranslationCollection
    {
        return $this->taxRuleTypeTranslations;
    }

    public function setTaxRuleTypeTranslations(TaxRuleTypeTranslationCollection $taxRuleTypeTranslations): void
    {
        $this->taxRuleTypeTranslations = $taxRuleTypeTranslations;
    }

    public function getProductCrossSellingTranslations(): ?ProductCrossSellingTranslationCollection
    {
        return $this->productCrossSellingTranslations;
    }

    public function setProductCrossSellingTranslations(ProductCrossSellingTranslationCollection $productCrossSellingTranslations): void
    {
        $this->productCrossSellingTranslations = $productCrossSellingTranslations;
    }

    public function getImportExportProfileTranslations(): ?ImportExportProfileTranslationCollection
    {
        return $this->importExportProfileTranslations;
    }

    public function setImportExportProfileTranslations(ImportExportProfileTranslationCollection $importExportProfileTranslations): void
    {
        $this->importExportProfileTranslations = $importExportProfileTranslations;
    }

    public function getProductFeatureSetTranslations(): ?ProductFeatureSetTranslationCollection
    {
        return $this->productFeatureSetTranslations;
    }

    public function setProductFeatureSetTranslations(ProductFeatureSetTranslationCollection $productFeatureSetTranslations): void
    {
        $this->productFeatureSetTranslations = $productFeatureSetTranslations;
    }

    public function getAppTranslations(): ?AppTranslationCollection
    {
        return $this->appTranslations;
    }

    public function setAppTranslations(AppTranslationCollection $appTranslations): void
    {
        $this->appTranslations = $appTranslations;
    }

    public function getActionButtonTranslations(): ?ActionButtonTranslationCollection
    {
        return $this->actionButtonTranslations;
    }

    public function setActionButtonTranslations(ActionButtonTranslationCollection $actionButtonTranslations): void
    {
        $this->actionButtonTranslations = $actionButtonTranslations;
    }

    public function getProductSortingTranslations(): ?ProductSortingTranslationCollection
    {
        return $this->productSortingTranslations;
    }

    public function setProductSortingTranslations(ProductSortingTranslationCollection $productSortingTranslations): void
    {
        $this->productSortingTranslations = $productSortingTranslations;
    }

    public function getProductSearchConfig(): ?ProductSearchConfigEntity
    {
        return $this->productSearchConfig;
    }

    public function setProductSearchConfig(ProductSearchConfigEntity $productSearchConfig): void
    {
        $this->productSearchConfig = $productSearchConfig;
    }

    public function getLandingPageTranslations(): ?LandingPageTranslationCollection
    {
        return $this->landingPageTranslations;
    }

    public function setLandingPageTranslations(LandingPageTranslationCollection $landingPageTranslations): void
    {
        $this->landingPageTranslations = $landingPageTranslations;
    }

    public function getAppCmsBlockTranslations(): ?AppCmsBlockTranslationCollection
    {
        return $this->appCmsBlockTranslations;
    }

    public function setAppCmsBlockTranslations(AppCmsBlockTranslationCollection $appCmsBlockTranslations): void
    {
        $this->appCmsBlockTranslations = $appCmsBlockTranslations;
    }

    public function getAppScriptConditionTranslations(): ?AppScriptConditionTranslationCollection
    {
        return $this->appScriptConditionTranslations;
    }

    public function setAppScriptConditionTranslations(AppScriptConditionTranslationCollection $appScriptConditionTranslations): void
    {
        $this->appScriptConditionTranslations = $appScriptConditionTranslations;
    }

    public function getAppFlowActionTranslations(): ?AppFlowActionTranslationCollection
    {
        return $this->appFlowActionTranslations;
    }

    public function setAppFlowActionTranslations(AppFlowActionTranslationCollection $appFlowActionTranslations): void
    {
        $this->appFlowActionTranslations = $appFlowActionTranslations;
    }

    public function getApiAlias(): string
    {
        return 'language';
    }

    public function getTaxProviderTranslations(): ?TaxProviderTranslationCollection
    {
        return $this->taxProviderTranslations;
    }

    public function setTaxProviderTranslations(TaxProviderTranslationCollection $taxProviderTranslations): void
    {
        $this->taxProviderTranslations = $taxProviderTranslations;
    }
}
