<?php declare(strict_types=1);

namespace Shuwei\Core\System\Snippet\Files;

use Shuwei\Core\Framework\Log\Package;

#[Package('system-settings')]
abstract class AbstractSnippetFile
{
    /**
     * Returns the displayed name.
     *
     * Example:
     * frontend.zh-CN
     */
    abstract public function getName(): string;

    /**
     * Returns the path to the json language file.
     *
     * Example:
     * /appPath/subDirectory/frontend.zh-CN.json
     */
    abstract public function getPath(): string;

    /**
     * Returns the associated language ISO.
     *
     * Example:
     * zh-CN
     * en-US
     */
    abstract public function getIso(): string;

    /**
     * Return the snippet author, which will be used when editing a file snippet in a snippet set
     *
     * Example:
     * shuwei
     * pluginName
     */
    abstract public function getAuthor(): string;

    /**
     * Returns a boolean which determines if its a base language file
     */
    abstract public function isBase(): bool;

    /**
     * Returns a technical name of the bundle or app that the file is belonged to
     */
    abstract public function getTechnicalName(): string;
}
