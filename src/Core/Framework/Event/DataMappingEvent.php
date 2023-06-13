<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Event;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Validation\DataBag\DataBag;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('business-ops')]
class DataMappingEvent extends Event implements ShuweiEvent
{
    public function __construct(
        private readonly DataBag $input,
        private array $output,
        private readonly Context $context
    ) {
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getInput(): DataBag
    {
        return $this->input;
    }

    public function getOutput(): array
    {
        return $this->output;
    }

    public function setOutput(array $output): void
    {
        $this->output = $output;
    }
}
