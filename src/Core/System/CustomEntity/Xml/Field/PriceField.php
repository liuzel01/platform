<?php declare(strict_types=1);

namespace Shuwei\Core\System\CustomEntity\Xml\Field;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\CustomEntity\Xml\Field\Traits\RequiredTrait;

/**
 * @internal
 */
#[Package('core')]
class PriceField extends Field
{
    use RequiredTrait;

    protected string $type = 'price';

    /**
     * @internal
     */
    public static function fromXml(\DOMElement $element): Field
    {
        return new self(self::parse($element));
    }
}
