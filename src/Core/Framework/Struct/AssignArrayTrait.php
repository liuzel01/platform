<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Struct;

use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
trait AssignArrayTrait
{
    /**
     * @param array<mixed> $options
     *
     * @return $this
     */
    public function assign(array $options)
    {
        foreach ($options as $key => $value) {
            if ($key === 'id' && method_exists($this, 'setId')) {
                $this->setId($value);

                continue;
            }

            try {
                $this->$key = $value; /* @phpstan-ignore-line */
            } catch (\Error|\Exception $error) {
                // nth
            }
        }

        return $this;
    }
}
