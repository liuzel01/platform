<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\Response;

use Shuwei\Core\Framework\Struct\ArrayStruct;

class NoContentResponse extends ApiResponse
{
    /**
     * @var ArrayStruct<string, mixed>
     */
    protected $object;

    public function __construct()
    {
        parent::__construct(new ArrayStruct());
        $this->setStatusCode(self::HTTP_NO_CONTENT);
    }
}