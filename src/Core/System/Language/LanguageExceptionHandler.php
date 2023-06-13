<?php declare(strict_types=1);

namespace Shuwei\Core\System\Language;

use Shuwei\Core\Framework\DataAbstractionLayer\Dbal\ExceptionHandlerInterface;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\Language\Exception\LanguageForeignKeyDeleteException;

#[Package('system-settings')]
class LanguageExceptionHandler implements ExceptionHandlerInterface
{
    public function getPriority(): int
    {
        return ExceptionHandlerInterface::PRIORITY_LATE;
    }

    public function matchException(\Exception $e): ?\Exception
    {
        if (preg_match('/SQLSTATE\[23000\]:.*(1217|1216).*a foreign key constraint/', $e->getMessage())) {
            return new LanguageForeignKeyDeleteException($e);
        }

        return null;
    }
}
