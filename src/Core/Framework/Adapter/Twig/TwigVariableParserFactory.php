<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Adapter\Twig;

use Shuwei\Core\Framework\Log\Package;
use Twig\Environment;

#[Package('core')]
class TwigVariableParserFactory
{
    public function getParser(Environment $twig): TwigVariableParser
    {
        return new TwigVariableParser($twig);
    }
}
