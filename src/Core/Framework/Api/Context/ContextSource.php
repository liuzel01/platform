<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\Context;

use Shuwei\Core\Framework\Log\Package;
use Symfony\Component\Serializer\Annotation\DiscriminatorMap;

#[DiscriminatorMap(typeProperty: 'type', mapping: ['system' => SystemSource::class, 'admin-api' => AdminApiSource::class])]
#[Package('core')]
interface ContextSource
{
}
