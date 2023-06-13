<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Update\Checkers;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Store\Services\StoreClient;
use Shuwei\Core\Framework\Update\Struct\ValidationResult;
use Shuwei\Core\System\SystemConfig\SystemConfigService;

#[Package('system-settings')]
class LicenseCheck
{
    /**
     * @internal
     */
    public function __construct(
        private readonly SystemConfigService $systemConfigService,
        private readonly StoreClient $storeClient
    ) {
    }

    public function check(): ValidationResult
    {
        $licenseHost = $this->systemConfigService->get('core.store.licenseHost');

        if (empty($licenseHost) || $this->storeClient->isShopUpgradeable()) {
            return new ValidationResult('validShuweiLicense', true, 'validShuweiLicense');
        }

        return new ValidationResult('invalidShuweiLicense', false, 'invalidShuweiLicense');
    }
}
