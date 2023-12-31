<?php declare(strict_types=1);

namespace Shuwei\Core\Installer\Controller;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Installer\Requirements\RequirementsValidatorInterface;
use Shuwei\Core\Installer\Requirements\Struct\RequirementsCheckCollection;
use Shuwei\Core\Maintenance\System\Service\JwtCertificateGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @internal
 */
#[Package('core')]
class RequirementsController extends InstallerController
{
    private readonly string $jwtDir;

    /**
     * @param iterable|RequirementsValidatorInterface[] $validators
     */
    public function __construct(
        private readonly iterable $validators,
        private readonly JwtCertificateGenerator $jwtCertificateGenerator,
        string $projectDir
    ) {
        $this->jwtDir = $projectDir . '/config/jwt';
    }

    #[Route(path: '/installer/requirements', name: 'installer.requirements', methods: ['GET', 'POST'])]
    public function requirements(Request $request): Response
    {
        $checks = new RequirementsCheckCollection();

        foreach ($this->validators as $validator) {
            $checks = $validator->validateRequirements($checks);
        }

        if ($request->isMethod('POST')) {
            return $this->redirectToRoute('installer.database-configuration');
        }

        return $this->renderInstaller('@Installer/installer/requirements.html.twig', ['requirementChecks' => $checks]);
    }
}
