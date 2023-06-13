<?php declare(strict_types=1);

namespace Shuwei\Core\System\CustomEntity\Xml\Config\AdminUi\XmlElements;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\CustomEntity\Xml\Config\ConfigXmlElement;

/**
 * Represents the XML detail element
 *
 * admin-ui > entity > detail
 *
 * @internal
 */
#[Package('content')]
final class Detail extends ConfigXmlElement
{
    private function __construct(
        protected readonly Tabs $tabs
    ) {
    }

    public static function fromXml(\DOMElement $element): self
    {
        return new self(Tabs::fromXml($element));
    }

    public function getTabs(): Tabs
    {
        return $this->tabs;
    }
}
