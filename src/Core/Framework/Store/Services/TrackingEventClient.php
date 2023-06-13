<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Services;

use GuzzleHttp\ClientInterface;
use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('merchant-services')]
class TrackingEventClient
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly InstanceService $instanceService
    ) {
    }

    /**
     * @param mixed[] $additionalData
     *
     * @return array<string, mixed>|null
     */
    public function fireTrackingEvent(string $eventName, array $additionalData = []): ?array
    {
        if (!$this->instanceService->getInstanceId()) {
            return null;
        }

        $additionalData['shuweiVersion'] = $this->instanceService->getShuweiVersion();
        $payload = [
            'additionalData' => $additionalData,
            'instanceId' => $this->instanceService->getInstanceId(),
            'event' => $eventName,
        ];

        try {
            $response = $this->client->request('POST', '/swplatform/tracking/events', ['json' => $payload]);

            return json_decode($response->getBody()->getContents(), true, flags: \JSON_THROW_ON_ERROR);
        } catch (\Exception) {
        }

        return null;
    }
}
