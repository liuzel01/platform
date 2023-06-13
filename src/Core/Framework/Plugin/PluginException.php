<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Plugin;

use Shuwei\Core\Framework\HttpException;
use Shuwei\Core\Framework\Log\Package;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class PluginException extends HttpException
{
    public const CANNOT_DELETE_COMPOSER_MANAGED = 'FRAMEWORK__PLUGIN_CANNOT_DELETE_COMPOSER_MANAGED';

    public static function cannotDeleteManaged(string $pluginName): self
    {
        return new self(
            Response::HTTP_BAD_REQUEST,
            self::CANNOT_DELETE_COMPOSER_MANAGED,
            'Plugin {{ name }} is managed by Composer and cannot be deleted',
            ['name' => $pluginName]
        );
    }
}
