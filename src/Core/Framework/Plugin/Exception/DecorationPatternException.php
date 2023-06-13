<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Plugin\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class DecorationPatternException extends ShuweiHttpException
{
    /**
     * @var string
     */
    protected $class;

    public function __construct(string $class)
    {
        parent::__construct(sprintf(
            'The getDecorated() function of core class %s cannot be used. This class is the base class.',
            $class
        ));
    }

    public function getErrorCode(): string
    {
        return (string) Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}
