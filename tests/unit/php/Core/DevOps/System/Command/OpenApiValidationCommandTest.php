<?php declare(strict_types=1);

namespace Shuwei\Tests\Unit\Core\DevOps\System\Command;

use AsyncAws\Core\Test\Http\SimpleMockedResponse;
use Shuwei\Core\DevOps\System\Command\OpenApiValidationCommand;
use Shuwei\Core\Framework\Api\ApiDefinition\DefinitionService;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpClient\MockHttpClient;

/**
 * @internal
 *
 * @covers \Shuwei\Core\DevOps\System\Command\OpenApiValidationCommand
 */
class OpenApiValidationCommandTest extends \PHPUnit\Framework\TestCase
{
    public function testRunWithoutErrors(): void
    {
        $command = new OpenApiValidationCommand(
            new MockHttpClient([new SimpleMockedResponse('{"messages": [], "schemaValidationMessages": []}', [])]),
            $this->createMock(DefinitionService::class)
        );
        $tester = new CommandTester($command);

        $tester->execute([]);

        static::assertSame($tester->getStatusCode(), 0);
    }

    public function testRunWithErrors(): void
    {
        $command = new OpenApiValidationCommand(
            new MockHttpClient(
                [new SimpleMockedResponse(json_encode([
                    'schemaValidationMessages' => [
                        [
                            'level' => 'error',
                            'domain' => 'validation',
                            'keyword' => 'oneOf',
                            'message' => 'instance failed to match exactly one schema (matched 0 out of 2)',
                            'schema' => [
                                'loadingURI' => '#',
                                'pointer' => "\/definitions\/Components\/properties\/schemas\/patternProperties\/^[a-zA-Z0-9\\.\\-_]+$",
                            ],
                            'instance' => [
                                'pointer' => "\/components\/schemas\/foo",
                            ],
                        ],
                    ],
                    'messages' => [],
                ], \JSON_THROW_ON_ERROR), [])]
            ),
            $this->createMock(DefinitionService::class)
        );
        $tester = new CommandTester($command);

        $tester->execute([]);

        static::assertSame($tester->getStatusCode(), 1);
    }
}
