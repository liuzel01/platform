<?php

declare(strict_types=1);

namespace Shuwei\Core\Framework\Test\Store\Command;

use PHPUnit\Framework\TestCase;
use Shuwei\Core\Framework\Store\Command\StoreLoginCommand;
use Shuwei\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 */
class StoreLoginCommandTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testEmptyPasswordOption(): void
    {
        $commandTester = new CommandTester($this->getContainer()->get(StoreLoginCommand::class));

        static::expectException(\RuntimeException::class);
        static::expectExceptionMessage('The password cannot be empty');

        $commandTester->setInputs(['', '', '']);
        $commandTester->execute([
            '--shuweiId' => 'no-reply@shuwei.de',
            '--user' => 'missing_user',
        ]);
    }

    public function testValidPasswordOptionInvalidUserOption(): void
    {
        $commandTester = new CommandTester($this->getContainer()->get(StoreLoginCommand::class));

        static::expectException(\RuntimeException::class);
        static::expectExceptionMessage('User not found');

        $commandTester->setInputs(['non-empty-password']);
        $commandTester->execute([
            '--shuweiId' => 'no-reply@shuwei.de',
            '--user' => 'missing_user',
        ]);
    }
}
