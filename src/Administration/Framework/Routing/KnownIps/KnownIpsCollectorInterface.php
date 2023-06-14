<?php declare(strict_types=1);

namespace Shuwei\Administration\Framework\Routing\KnownIps;

use Shuwei\Core\Framework\Log\Package;
use Symfony\Component\HttpFoundation\Request;

#[Package('administration')]
interface KnownIpsCollectorInterface
{
    public function collectIps(Request $request): array;
}
