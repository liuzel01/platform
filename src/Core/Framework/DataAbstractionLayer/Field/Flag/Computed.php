<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag;

use Shuwei\Core\Framework\Log\Package;

/**
 * The value is computed by indexer or external systems and
 * cannot be written using the DAL.
 */
#[Package('core')]
class Computed extends Flag
{
    public function parse(): \Generator
    {
        yield 'computed' => true;
    }
}
