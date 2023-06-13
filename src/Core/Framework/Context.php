<?php declare(strict_types=1);

namespace Shuwei\Core\Framework;

use Shuwei\Core\Defaults;
use Shuwei\Core\Framework\Api\Context\AdminApiSource;
use Shuwei\Core\Framework\Api\Context\ContextSource;
use Shuwei\Core\Framework\Api\Context\SystemSource;
use Shuwei\Core\Framework\DataAbstractionLayer\Pricing\CashRoundingConfig;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Struct\StateAwareTrait;
use Shuwei\Core\Framework\Struct\Struct;

#[Package('core')]
class Context extends Struct
{
    use StateAwareTrait;

    final public const SYSTEM_SCOPE = 'system';
    final public const USER_SCOPE = 'user';
    final public const CRUD_API_SCOPE = 'crud';

    /**
     * @var non-empty-array<string>
     */
    protected array $languageIdChain;

    protected string $scope = self::USER_SCOPE;

    protected bool $rulesLocked = false;

    /**
     * @param array<string> $languageIdChain
     * @param array<string> $ruleIds
     */
    public function __construct(
        protected ContextSource $source,
        protected array $ruleIds = [],
        array $languageIdChain = [Defaults::LANGUAGE_SYSTEM],
        protected string $versionId = Defaults::LIVE_VERSION,
        protected bool $considerInheritance = false,
    ) {
        if ($source instanceof SystemSource) {
            $this->scope = self::SYSTEM_SCOPE;
        }

        if (empty($languageIdChain)) {
            throw new \InvalidArgumentException('Argument languageIdChain must not be empty');
        }

        /** @var non-empty-array<string> $chain */
        $chain = array_keys(array_flip(array_filter($languageIdChain)));
        $this->languageIdChain = $chain;
    }

    /**
     * @internal
     */
    public static function createDefaultContext(?ContextSource $source = null): self
    {
        $source ??= new SystemSource();

        return new self($source);
    }

    public function getSource(): ContextSource
    {
        return $this->source;
    }

    public function getVersionId(): string
    {
        return $this->versionId;
    }

    public function getLanguageId(): string
    {
        return $this->languageIdChain[0];
    }

    /**
     * @return array<string>
     */
    public function getRuleIds(): array
    {
        return $this->ruleIds;
    }

    /**
     * @return non-empty-array<string>
     */
    public function getLanguageIdChain(): array
    {
        return $this->languageIdChain;
    }

    public function createWithVersionId(string $versionId): self
    {
        $context = new self(
            $this->source,
            $this->ruleIds,
            $this->languageIdChain,
            $versionId,
            $this->considerInheritance,
        );
        $context->scope = $this->scope;

        foreach ($this->getExtensions() as $key => $extension) {
            $context->addExtension($key, $extension);
        }

        return $context;
    }

    /**
     * @param callable(Context): mixed $callback
     *
     * @return mixed the return value of the provided callback function
     */
    public function scope(string $scope, callable $callback)
    {
        $currentScope = $this->getScope();
        $this->scope = $scope;

        try {
            $result = $callback($this);
        } finally {
            $this->scope = $currentScope;
        }

        return $result;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function considerInheritance(): bool
    {
        return $this->considerInheritance;
    }
    public function isAllowed(string $privilege): bool
    {
        if ($this->source instanceof AdminApiSource) {
            return $this->source->isAllowed($privilege);
        }

        return true;
    }

    /**
     * @param array<string> $ruleIds
     */
    public function setRuleIds(array $ruleIds): void
    {
        if ($this->rulesLocked) {
            throw new ContextRulesLockedException();
        }

        $this->ruleIds = array_filter(array_values($ruleIds));
    }

    /**
     * @param callable(Context): mixed $function
     *
     * @return mixed
     */
    public function enableInheritance(callable $function)
    {
        $previous = $this->considerInheritance;
        $this->considerInheritance = true;
        $result = $function($this);
        $this->considerInheritance = $previous;

        return $result;
    }

    /**
     * @param callable(Context): mixed $function
     *
     * @return mixed
     */
    public function disableInheritance(callable $function)
    {
        $previous = $this->considerInheritance;
        $this->considerInheritance = false;
        $result = $function($this);
        $this->considerInheritance = $previous;

        return $result;
    }

    public function getApiAlias(): string
    {
        return 'context';
    }

    public function getRounding(): CashRoundingConfig
    {
        return $this->rounding;
    }
    public function lockRules(): void
    {
        $this->rulesLocked = true;
    }
}
