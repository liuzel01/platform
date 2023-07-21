<?php declare(strict_types=1);

namespace Shuwei\Core\Maintenance\Test\User\Command;

use PHPUnit\Framework\TestCase;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shuwei\Core\Framework\Uuid\Uuid;
use Shuwei\Core\Maintenance\User\Command\UserChangePasswordCommand;
use Shuwei\Core\System\User\UserEntity;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 */
#[Package('core')]
class UserChangePasswordCommandTest extends TestCase
{
    use IntegrationTestBehaviour;
    private const TEST_USERNAME = 'shuwei';
    private const TEST_PASSWORD = 'shuweiPassword';

    /**
     * @var EntityRepository
     */
    private $userRepository;

    private Context $context;

    protected function setUp(): void
    {
        $this->userRepository = $this->getContainer()->get('user.repository');
        $this->context = Context::createDefaultContext();
    }

    public function testUnknownUser(): void
    {
        $commandTester = new CommandTester($this->getContainer()->get(UserChangePasswordCommand::class));
        $commandTester->execute([
            'username' => self::TEST_USERNAME,
            '--password' => self::TEST_PASSWORD,
        ]);

        $expected = 'The user "' . self::TEST_USERNAME . '" does not exist.';
        static::assertStringContainsString($expected, $commandTester->getDisplay());
        static::assertEquals(1, $commandTester->getStatusCode());
    }



    public function testEmptyPasswordOption(): void
    {
        $commandTester = new CommandTester($this->getContainer()->get(UserChangePasswordCommand::class));

        static::expectException(\RuntimeException::class);
        static::expectExceptionMessage('The password cannot be empty');

        $commandTester->setInputs(['', '', '']);
        $commandTester->execute([
            'username' => self::TEST_USERNAME,
        ]);
    }

    private function createUser(): string
    {
        $uuid = Uuid::randomHex();

        $this->userRepository->create([
            [
                'id' => $uuid,
                'localeId' => $this->getLocaleIdOfSystemLanguage(),
                'username' => self::TEST_USERNAME,
                'password' => self::TEST_PASSWORD,
                'firstName' => sprintf('Foo%s', Uuid::randomHex()),
                'lastName' => sprintf('Bar%s', Uuid::randomHex()),
                'email' => sprintf('%s@foo.bar', $uuid),
            ],
        ], $this->context);

        return $uuid;
    }
}
