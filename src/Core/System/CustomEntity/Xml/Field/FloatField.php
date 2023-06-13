<?php declare(strict_types=1);

namespace Shuwei\Core\System\CustomEntity\Xml\Field;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\CustomEntity\Xml\Field\Traits\RequiredTrait;
use Shuwei\Core\System\CustomEntity\Xml\Field\Traits\TranslatableTrait;

/**
 * @internal
 */
#[Package('core')]
class FloatField extends Field
{
    use RequiredTrait;
    use TranslatableTrait;

    protected string $type = 'float';

    /**
     * @internal
     */
    public static function fromXml(\DOMElement $element): Field
    {
        return new self(self::parse($element));
    }
}
