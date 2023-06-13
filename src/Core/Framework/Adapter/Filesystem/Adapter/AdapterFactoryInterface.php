<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Adapter\Filesystem\Adapter;

use League\Flysystem\FilesystemAdapter;
use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
interface AdapterFactoryInterface
{
    public function create(array $config): FilesystemAdapter;

    public function getType(): string;
}
