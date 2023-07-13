<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Field;

use Shuwei\Core\Framework\Api\Context\AdminApiSource;
use Shuwei\Core\Framework\DataAbstractionLayer\Dbal\FieldAccessorBuilder\DefaultFieldAccessorBuilder;
use Shuwei\Core\Framework\DataAbstractionLayer\Dbal\FieldAccessorBuilder\FieldAccessorBuilderInterface;
use Shuwei\Core\Framework\DataAbstractionLayer\Dbal\FieldResolver\AbstractFieldResolver;
use Shuwei\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Flag;
use Shuwei\Core\Framework\DataAbstractionLayer\FieldSerializer\FieldSerializerInterface;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Struct\Struct;

#[Package('core')]
abstract class Field extends Struct
{
    /**
     * @var array<string, Flag>
     */
    protected array $flags = [];


    private ?FieldSerializerInterface $serializer = null;

    private ?AbstractFieldResolver $resolver = null;

    private ?FieldAccessorBuilderInterface $accessorBuilder = null;

    private ?DefinitionInstanceRegistry $registry = null;

    public function __construct(protected string $propertyName)
    {
        $this->addFlags(new ApiAware(AdminApiSource::class));
    }

    public function compile(DefinitionInstanceRegistry $registry): void
    {
        $this->registry = $registry;
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function getExtractPriority(): int
    {
        return 0;
    }

    public function setFlags(Flag ...$flags): self
    {
        $this->flags = [];
        foreach ($flags as $flag) {
            $this->flags[$flag::class] = $flag;
        }
        if (!$this->is(ApiAware::class)) {
            $this->addFlags(new ApiAware(AdminApiSource::class));
        }

        return $this;
    }

    public function addFlags(Flag ...$flags): self
    {
        foreach ($flags as $flag) {
            $this->flags[$flag::class] = $flag;
        }

        return $this;
    }

    public function removeFlag(string $class): self
    {
        unset($this->flags[$class]);

        return $this;
    }

    public function is(string $class): bool
    {
        return $this->getFlag($class) !== null;
    }

    public function getFlag(string $class): ?Flag
    {
        return $this->flags[$class] ?? null;
    }

    /**
     * @return list<Flag>
     */
    public function getFlags(): array
    {
        return array_values($this->flags);
    }

    public function getSerializer(): FieldSerializerInterface
    {
        $this->initLazy();

        \assert($this->serializer !== null);

        return $this->serializer;
    }

    /**
     * @return AbstractFieldResolver|null
     */
    public function getResolver()
    {
        $this->initLazy();

        return $this->resolver;
    }

    public function getAccessorBuilder(): ?FieldAccessorBuilderInterface
    {
        $this->initLazy();

        return $this->accessorBuilder;
    }
    /**
     * @phpstan-assert-if-true !null $this->registry
     */
    public function isCompiled(): bool
    {
        return $this->registry !== null;
    }
    /**
     * @return class-string<FieldSerializerInterface>
     */
    abstract protected function getSerializerClass(): string;
    /**
     * @return class-string<AbstractFieldResolver>|null
     */
    protected function getResolverClass(): ?string
    {
        return null;
    }
    /**
     * @return class-string<FieldAccessorBuilderInterface>|null
     */
    protected function getAccessorBuilderClass(): ?string
    {
        if ($this instanceof StorageAware) {
            return DefaultFieldAccessorBuilder::class;
        }

        return null;
    }

    private function initLazy(): void
    {
        if ($this->serializer !== null) {
            return;
        }

        \assert($this->registry !== null);

        $this->serializer = $this->registry->getSerializer($this->getSerializerClass());

        $resolverClass = $this->getResolverClass();
        if ($resolverClass !== null) {
            $this->resolver = $this->registry->getResolver($resolverClass);
        }

        $accessorBuilderClass = $this->getAccessorBuilderClass();
        if ($accessorBuilderClass !== null) {
            $this->accessorBuilder = $this->registry->getAccessorBuilder($accessorBuilderClass);
        }
    }
}
