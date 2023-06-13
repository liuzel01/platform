<?php declare(strict_types=1);

namespace Shuwei\Core\Maintenance\System\Exception;

use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
class JwtCertificateGenerationException extends \RuntimeException
{
}
