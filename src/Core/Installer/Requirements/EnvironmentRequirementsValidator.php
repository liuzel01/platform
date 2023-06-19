<?php declare(strict_types=1);

namespace Shuwei\Core\Installer\Requirements;

use Composer\Composer;
use Composer\Repository\PlatformRepository;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Installer\Requirements\Struct\RequirementCheck;
use Shuwei\Core\Installer\Requirements\Struct\RequirementsCheckCollection;
use Shuwei\Core\Installer\Requirements\Struct\SystemCheck;

/**
 * @internal
 */
#[Package('core')]
class EnvironmentRequirementsValidator implements RequirementsValidatorInterface
{
    public function __construct(
        private readonly Composer $composer,
        private readonly PlatformRepository $systemEnvironment
    ) {
    }

    public function validateRequirements(RequirementsCheckCollection $checks): RequirementsCheckCollection
    {
        $platform = $this->composer->getRepositoryManager()->getLocalRepository()->findPackage('shuwei/platform', '*');
        if (!$platform) {
            $platform = $this->composer->getRepositoryManager()->getLocalRepository()->findPackage('shuwei/core', '*');
        }
        if (!$platform) {
            $platform = $this->composer->getPackage();
        }

        foreach ($platform->getRequires() as $require => $link) {
            if (!PlatformRepository::isPlatformPackage($require)) {
                continue;
            }

            $result = $this->systemEnvironment->findPackage($require, $link->getConstraint());

            if ($result) {
                $checks->add(new SystemCheck(
                    $require,
                    RequirementCheck::STATUS_SUCCESS,
                    $link->getConstraint()->getPrettyString(),
                    $result->getPrettyVersion()
                ));

                continue;
            }

            $extension = $this->systemEnvironment->findPackage($require, '*');

            if ((string) $link->getConstraint() === '*' || !$extension) {
                $checks->add(new SystemCheck(
                    $require,
                    RequirementCheck::STATUS_ERROR,
                    $link->getConstraint()->getPrettyString(),
                    '-'
                ));

                continue;
            }

            $checks->add(new SystemCheck(
                $require,
                RequirementCheck::STATUS_ERROR,
                $link->getConstraint()->getPrettyString(),
                $extension->getPrettyVersion()
            ));
        }

        return $checks;
    }
}
