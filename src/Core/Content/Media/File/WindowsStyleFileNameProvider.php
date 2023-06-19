<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\File;

use Shuwei\Core\Content\Media\MediaCollection;
use Shuwei\Core\Framework\Log\Package;

#[Package('content')]
class WindowsStyleFileNameProvider extends FileNameProvider
{
    protected function getNextFileName(string $originalFileName, MediaCollection $relatedMedia, int $iteration): string
    {
        $suffix = $iteration === 0 ? '' : "_($iteration)";

        return $originalFileName . $suffix;
    }
}
