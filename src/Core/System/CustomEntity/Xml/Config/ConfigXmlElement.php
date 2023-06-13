<?php declare(strict_types=1);

namespace Shuwei\Core\System\CustomEntity\Xml\Config;

use Shuwei\Core\Framework\App\Manifest\Xml\XmlElement;
use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('content')]
abstract class ConfigXmlElement extends XmlElement
{
    abstract public static function fromXml(\DOMElement $element): self;

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();
        unset($data['extensions']);

        return $data;
    }
}
