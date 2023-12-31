<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Struct;

use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
trait CreateFromTrait
{
    /**
     * tag:v6.6.0 - Return type will be changed to native type `static`
     *
     * @return static
     */
    #[\ReturnTypeWillChange]
    public static function createFrom(Struct $object)
    {
        try {
            $self = (new \ReflectionClass(static::class))
                ->newInstanceWithoutConstructor();
        } catch (\ReflectionException $exception) {
            throw new \InvalidArgumentException($exception->getMessage());
        }

        foreach (get_object_vars($object) as $property => $value) {
            $self->$property = $value; /* @phpstan-ignore-line */
        }

        return $self;
    }
}
