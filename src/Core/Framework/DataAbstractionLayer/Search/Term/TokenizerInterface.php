<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Search\Term;

use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
interface TokenizerInterface
{
    /**
     * @return array<string>
     */
    public function tokenize(string $string): array;
}
