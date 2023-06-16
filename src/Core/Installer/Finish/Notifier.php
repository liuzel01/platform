<?php declare(strict_types=1);

namespace Shuwei\Core\Installer\Finish;

use GuzzleHttp\Client;
use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
class Notifier
{
    final public const EVENT_INSTALL_STARTED = 'Installer started';
    final public const EVENT_INSTALL_FINISHED = 'Installer finished';

    public function __construct(
        private readonly string $apiEndPoint,
        private readonly UniqueIdGenerator $idGenerator,
        private readonly Client $client,
        private readonly string $shuweiVersion
    ) {
    }

    /**
     * @param array<string, string> $additionalInformation
     */
    public function doTrackEvent(string $eventName, array $additionalInformation = []): void
    {
        $additionalInformation['shuweiVersion'] = $this->shuweiVersion;
        $payload = [
            'additionalData' => $additionalInformation,
            'instanceId' => $this->idGenerator->getUniqueId(),
            'event' => $eventName,
        ];

        try {
            $this->client->postAsync($this->apiEndPoint . '/swplatform/tracking/events', ['json' => $payload]);
        } catch (\Exception) {
            // ignore
        }
    }
}
