<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Write\FieldException;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiException;

#[Package('core')]
interface WriteFieldException extends ShuweiException
{
    public function getPath(): string;
}
