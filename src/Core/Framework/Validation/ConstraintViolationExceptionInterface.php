<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Validation;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiException;
use Symfony\Component\Validator\ConstraintViolationList;

#[Package('core')]
interface ConstraintViolationExceptionInterface extends ShuweiException
{
    public function getViolations(): ConstraintViolationList;
}
