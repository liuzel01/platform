<?php declare(strict_types=1);

namespace Shuwei\Core\System\Country\Aggregate\CountryStateTranslation;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shuwei\Core\Framework\DataAbstractionLayer\TranslationEntity;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\Country\Aggregate\CountryState\CountryStateEntity;

#[Package('system-settings')]
class CountryStateTranslationEntity extends TranslationEntity
{
    use EntityCustomFieldsTrait;

    /**
     * @var string
     */
    protected $countryStateId;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var CountryStateEntity|null
     */
    protected $countryState;

    public function getCountryStateId(): string
    {
        return $this->countryStateId;
    }

    public function setCountryStateId(string $countryStateId): void
    {
        $this->countryStateId = $countryStateId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getCountryState(): ?CountryStateEntity
    {
        return $this->countryState;
    }

    public function setCountryState(CountryStateEntity $countryState): void
    {
        $this->countryState = $countryState;
    }
}
