<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\MediaType;

use Shuwei\Core\Framework\Log\Package;

#[Package('content')]
class DocumentType extends MediaType
{
    protected $name = 'DOCUMENT';
}
