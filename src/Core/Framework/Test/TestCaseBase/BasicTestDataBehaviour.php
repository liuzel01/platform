<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Test\TestCaseBase;

use Doctrine\DBAL\Connection;
use Shuwei\Core\Checkout\Order\OrderStates;
use Shuwei\Core\Checkout\Payment\PaymentMethodEntity;
use Shuwei\Core\Checkout\Shipping\ShippingMethodEntity;
use Shuwei\Core\Defaults;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shuwei\Core\Framework\Uuid\Uuid;
use Shuwei\Core\System\Language\LanguageEntity;
use Shuwei\Core\Test\TestDefaults;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait BasicTestDataBehaviour
{
    public function getDeDeLanguageId(): string
    {
        /** @var EntityRepository $repository */
        $repository = $this->getContainer()->get('language.repository');

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('language.translationCode.code', 'en-US'));

        /** @var LanguageEntity $language */
        $language = $repository->search($criteria, Context::createDefaultContext())->first();

        return $language->getId();
    }

    abstract protected static function getContainer(): ContainerInterface;

    protected function getValidPaymentMethodId(?string $websiteId = null): string
    {
        /** @var EntityRepository $repository */
        $repository = $this->getContainer()->get('payment_method.repository');

        $criteria = (new Criteria())
            ->setLimit(1)
            ->addFilter(new EqualsFilter('active', true));

        if ($websiteId) {
            $criteria->addFilter(new EqualsFilter('websites.id', $websiteId));
        }

        /** @var string $id */
        $id = $repository->searchIds($criteria, Context::createDefaultContext())->firstId();

        return $id;
    }

    protected function getInactivePaymentMethodId(?string $websiteId = null): string
    {
        /** @var EntityRepository $repository */
        $repository = $this->getContainer()->get('payment_method.repository');

        $criteria = (new Criteria())
            ->setLimit(1)
            ->addFilter(new EqualsFilter('active', false));

        if ($websiteId) {
            $criteria->addFilter(new EqualsFilter('websites.id', $websiteId));
        }

        /** @var string $id */
        $id = $repository->searchIds($criteria, Context::createDefaultContext())->firstId();

        return $id;
    }

    protected function getAvailablePaymentMethod(?string $websiteId = null): PaymentMethodEntity
    {
        /** @var EntityRepository $repository */
        $repository = $this->getContainer()->get('payment_method.repository');

        $criteria = (new Criteria())
            ->setLimit(1)
            ->addFilter(new EqualsFilter('active', true))
            ->addFilter(new EqualsFilter('availabilityRuleId', null));

        if ($websiteId) {
            $criteria->addFilter(new EqualsFilter('websites.id', $websiteId));
        }

        /** @var PaymentMethodEntity|null $paymentMethod */
        $paymentMethod = $repository->search($criteria, Context::createDefaultContext())->getEntities()->first();

        if ($paymentMethod === null) {
            throw new \LogicException('No available Payment method configured');
        }

        return $paymentMethod;
    }

    protected function getValidShippingMethodId(?string $websiteId = null): string
    {
        /** @var EntityRepository $repository */
        $repository = $this->getContainer()->get('shipping_method.repository');

        $criteria = (new Criteria())
            ->setLimit(1)
            ->addFilter(new EqualsFilter('active', true))
            ->addSorting(new FieldSorting('name'));

        if ($websiteId) {
            $criteria->addFilter(new EqualsFilter('websites.id', $websiteId));
        }

        /** @var string $id */
        $id = $repository->searchIds($criteria, Context::createDefaultContext())->firstId();

        return $id;
    }

    protected function getAvailableShippingMethod(?string $websiteId = null): ShippingMethodEntity
    {
        /** @var EntityRepository $repository */
        $repository = $this->getContainer()->get('shipping_method.repository');

        $criteria = (new Criteria())
            ->addAssociation('prices')
            ->addFilter(new EqualsFilter('shipping_method.prices.calculation', 1))
            ->addFilter(new EqualsFilter('active', true))
            ->addSorting(new FieldSorting('name'));

        if ($websiteId) {
            $criteria->addFilter(new EqualsFilter('websites.id', $websiteId));
        }

        $shippingMethods = $repository->search($criteria, Context::createDefaultContext())->getEntities();

        /** @var ShippingMethodEntity $shippingMethod */
        foreach ($shippingMethods as $shippingMethod) {
            if ($shippingMethod->getAvailabilityRuleId() !== null) {
                return $shippingMethod;
            }
        }

        throw new \LogicException('No available ShippingMethod configured');
    }

    protected function getValidSalutationId(): string
    {
        /** @var EntityRepository $repository */
        $repository = $this->getContainer()->get('salutation.repository');

        $criteria = (new Criteria())
            ->setLimit(1)
            ->addSorting(new FieldSorting('salutationKey'));

        /** @var string $id */
        $id = $repository->searchIds($criteria, Context::createDefaultContext())->firstId();

        return $id;
    }

    protected function getLocaleIdOfSystemLanguage(): string
    {
        /** @var EntityRepository $repository */
        $repository = $this->getContainer()->get('language.repository');

        /** @var LanguageEntity $language */
        $language = $repository->search(new Criteria([Defaults::LANGUAGE_SYSTEM]), Context::createDefaultContext())->get(Defaults::LANGUAGE_SYSTEM);

        return $language->getLocaleId();
    }

    protected function getSnippetSetIdForLocale(string $locale): ?string
    {
        /** @var EntityRepository $repository */
        $repository = $this->getContainer()->get('snippet_set.repository');

        $criteria = (new Criteria())
            ->addFilter(new EqualsFilter('iso', $locale))
            ->setLimit(1);

        return $repository->searchIds($criteria, Context::createDefaultContext())->firstId();
    }

    /**
     * @param string|null $websiteId (null when no saleschannel filtering)
     */
    protected function getValidCountryId(?string $websiteId = TestDefaults::SALES_CHANNEL): string
    {
        /** @var EntityRepository $repository */
        $repository = $this->getContainer()->get('country.repository');

        $criteria = (new Criteria())->setLimit(1)
            ->addFilter(new EqualsFilter('active', true))
            ->addFilter(new EqualsFilter('shippingAvailable', true))
            ->addSorting(new FieldSorting('iso'));

        if ($websiteId !== null) {
            $criteria->addFilter(new EqualsFilter('websites.id', $websiteId));
        }

        $result = $repository->search($criteria, Context::createDefaultContext());

        /** @var string $id */
        $id = $repository->searchIds($criteria, Context::createDefaultContext())->firstId();

        return $id;
    }

    protected function getDeCountryId(): string
    {
        /** @var EntityRepository $repository */
        $repository = $this->getContainer()->get('country.repository');

        $criteria = (new Criteria())->setLimit(1)
            ->addFilter(new EqualsFilter('iso', 'DE'));

        /** @var string $id */
        $id = $repository->searchIds($criteria, Context::createDefaultContext())->firstId();

        return $id;
    }

    protected function getValidCategoryId(): string
    {
        /** @var EntityRepository $repository */
        $repository = $this->getContainer()->get('category.repository');

        $criteria = (new Criteria())
            ->setLimit(1)
            ->addSorting(new FieldSorting('level'), new FieldSorting('name'));

        /** @var string $id */
        $id = $repository->searchIds($criteria, Context::createDefaultContext())->firstId();

        return $id;
    }

    protected function getValidTaxId(): string
    {
        /** @var EntityRepository $repository */
        $repository = $this->getContainer()->get('tax.repository');

        $criteria = (new Criteria())
            ->setLimit(1)
            ->addSorting(new FieldSorting('name'));

        /** @var string $id */
        $id = $repository->searchIds($criteria, Context::createDefaultContext())->firstId();

        return $id;
    }

    protected function getValidDocumentTypeId(): string
    {
        /** @var EntityRepository $repository */
        $repository = $this->getContainer()->get('document_type.repository');

        $criteria = (new Criteria())
            ->setLimit(1)
            ->addSorting(new FieldSorting('technicalName'));

        /** @var string $id */
        $id = $repository->searchIds($criteria, Context::createDefaultContext())->firstId();

        return $id;
    }

    protected function getStateMachineState(string $stateMachine = OrderStates::STATE_MACHINE, string $state = OrderStates::STATE_OPEN): string
    {
        /** @var EntityRepository $repository */
        $repository = $this->getContainer()->get('state_machine_state.repository');

        $criteria = new Criteria();
        $criteria
            ->setLimit(1)
            ->addFilter(new EqualsFilter('technicalName', $state))
            ->addFilter(new EqualsFilter('stateMachine.technicalName', $stateMachine));

        /** @var string $id */
        $id = $repository->searchIds($criteria, Context::createDefaultContext())->firstId();

        return $id;
    }

    protected function getCurrencyIdByIso(string $iso = 'EUR'): string
    {
        $connection = $this->getContainer()->get(Connection::class);

        return Uuid::fromBytesToHex($connection->fetchOne('SELECT id FROM currency WHERE iso_code = :iso', ['iso' => $iso]));
    }
}
