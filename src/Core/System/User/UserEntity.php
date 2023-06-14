<?php declare(strict_types=1);

namespace Shuwei\Core\System\User;

use Shuwei\Core\Checkout\Customer\CustomerCollection;
use Shuwei\Core\Checkout\Order\OrderCollection;
use Shuwei\Core\Content\ImportExport\Aggregate\ImportExportLog\ImportExportLogCollection;
use Shuwei\Core\Content\Media\MediaCollection;
use Shuwei\Core\Content\Media\MediaEntity;
use Shuwei\Core\Framework\Api\Acl\Role\AclRoleCollection;
use Shuwei\Core\Framework\DataAbstractionLayer\Entity;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\Locale\LocaleEntity;
use Shuwei\Core\System\StateMachine\Aggregation\StateMachineHistory\StateMachineHistoryCollection;
use Shuwei\Core\System\User\Aggregate\UserAccessKey\UserAccessKeyCollection;
use Shuwei\Core\System\User\Aggregate\UserConfig\UserConfigCollection;
use Shuwei\Core\System\User\Aggregate\UserRecovery\UserRecoveryEntity;

#[Package('system-settings')]
class UserEntity extends Entity
{
    use EntityCustomFieldsTrait;
    use EntityIdTrait;

    /**
     * @var string
     */
    protected string $localeId;

    /**
     * @var string|null
     */
    protected $avatarId;

    /**
     * @var string
     */
    protected string $username;

    /**
     * @internal
     *
     * @var string
     */
    protected string $password;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string|null
     */
    protected $title;

    /**
     * @var string
     */
    protected string $email;

    /**
     * @var bool
     */
    protected bool $active;

    /**
     * @var bool
     */
    protected bool $admin;

    /**
     * @var AclRoleCollection
     */
    protected $aclRoles;

    /**
     * @var LocaleEntity|null
     */
    protected $locale;




    /**
     * @internal
     *
     * @var string|null
     */
    protected $storeToken;

    /**
     * @var \DateTimeInterface|null
     */
    protected $lastUpdatedPasswordAt;



    protected string $timeZone;

    /**
     * @return string
     */
    public function getLocaleId(): string
    {
        return $this->localeId;
    }

    /**
     * @param string $localeId
     */
    public function setLocaleId(string $localeId): void
    {
        $this->localeId = $localeId;
    }

    /**
     * @return string|null
     */
    public function getAvatarId(): ?string
    {
        return $this->avatarId;
    }

    /**
     * @param string|null $avatarId
     */
    public function setAvatarId(?string $avatarId): void
    {
        $this->avatarId = $avatarId;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->admin;
    }

    /**
     * @param bool $admin
     */
    public function setAdmin(bool $admin): void
    {
        $this->admin = $admin;
    }

    /**
     * @return AclRoleCollection
     */
    public function getAclRoles(): AclRoleCollection
    {
        return $this->aclRoles;
    }

    /**
     * @param AclRoleCollection $aclRoles
     */
    public function setAclRoles(AclRoleCollection $aclRoles): void
    {
        $this->aclRoles = $aclRoles;
    }

    /**
     * @return LocaleEntity|null
     */
    public function getLocale(): ?LocaleEntity
    {
        return $this->locale;
    }

    /**
     * @param LocaleEntity|null $locale
     */
    public function setLocale(?LocaleEntity $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @return string|null
     */
    public function getStoreToken(): ?string
    {
        return $this->storeToken;
    }

    /**
     * @param string|null $storeToken
     */
    public function setStoreToken(?string $storeToken): void
    {
        $this->storeToken = $storeToken;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getLastUpdatedPasswordAt(): ?\DateTimeInterface
    {
        return $this->lastUpdatedPasswordAt;
    }

    /**
     * @param \DateTimeInterface|null $lastUpdatedPasswordAt
     */
    public function setLastUpdatedPasswordAt(?\DateTimeInterface $lastUpdatedPasswordAt): void
    {
        $this->lastUpdatedPasswordAt = $lastUpdatedPasswordAt;
    }

    /**
     * @return string
     */
    public function getTimeZone(): string
    {
        return $this->timeZone;
    }

    /**
     * @param string $timeZone
     */
    public function setTimeZone(string $timeZone): void
    {
        $this->timeZone = $timeZone;
    }


}
