<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag;

use Shuwei\Core\Framework\Log\Package;

/**
 * Flag a text column that an empty string should not be considered as null
 */
#[Package('core')]
class AllowEmptyString extends Flag
{
    public function parse(): \Generator
    {
        yield 'allow_empty_string' => true;
    }
}
