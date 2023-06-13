<?php declare(strict_types=1);

namespace Shuwei\Core\System\CustomEntity\Xml\Field;

use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
class ManyToManyField extends AssociationField
{
    protected string $type = 'many-to-many';

    protected string $onDelete = 'cascade';

    /**
     * @internal
     */
    public static function fromXml(\DOMElement $element): Field
    {
        return new self(self::parse($element));
    }
}
