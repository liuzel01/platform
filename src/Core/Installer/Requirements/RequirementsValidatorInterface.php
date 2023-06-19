<?php declare(strict_types=1);

namespace Shuwei\Core\Installer\Requirements;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Installer\Requirements\Struct\RequirementsCheckCollection;

/**
 * @internal
 */
#[Package('core')]
interface RequirementsValidatorInterface
{
    public function validateRequirements(RequirementsCheckCollection $checks): RequirementsCheckCollection;
}
