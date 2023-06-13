<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

/**
 * @internal
 */
#[Package('merchant-services')]
class ExtensionUpdateRequiresConsentAffirmationException extends ShuweiHttpException
{
    /**
     * @param array<string, array<string, mixed>> $deltas
     */
    public static function fromDelta(string $appName, array $deltas): self
    {
        return new self(
            'Updating app "{{appName}}" requires a renewed consent affirmation.',
            compact('appName', 'deltas')
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__EXTENSION_UPDATE_REQUIRES_CONSENT_AFFIRMATION';
    }
}
