<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Services;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shuwei\Core\Framework\Store\Struct\ReviewStruct;

/**
 * @internal
 */
#[Package('merchant-services')]
class ExtensionStoreLicensesService extends AbstractExtensionStoreLicensesService
{
    public function __construct(private readonly StoreClient $client)
    {
    }

    public function cancelSubscription(int $licenseId, Context $context): void
    {
        $this->client->cancelSubscription($licenseId, $context);
    }

    public function rateLicensedExtension(ReviewStruct $rating, Context $context): void
    {
        $this->client->createRating($rating, $context);
    }

    /**
     * @codeCoverageIgnore
     */
    protected function getDecorated(): AbstractExtensionStoreLicensesService
    {
        throw new DecorationPatternException(self::class);
    }
}
