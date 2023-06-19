<?php
declare(strict_types=1);

namespace Shuwei\WebInstaller\Controller;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\WebInstaller\Services\PhpBinaryFinder;
use Shuwei\WebInstaller\Services\RecoveryManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @internal
 */
#[Package('core')]
class PhpConfigController extends AbstractController
{
    public function __construct(
        private readonly PhpBinaryFinder $binaryFinder,
        private readonly RecoveryManager $recoveryManager
    ) {
    }

    #[Route('/configure', name: 'configure', defaults: ['step' => 1])]
    public function index(Request $request): Response
    {
        try {
            $shuweiLocation = $this->recoveryManager->getShuweiLocation();
        } catch (\RuntimeException $e) {
            $shuweiLocation = null;
        }

        if ($phpBinary = $request->request->get('phpBinary')) {
            // Reset the latest version to force a new check
            $request->getSession()->remove('latestVersion');

            $request->getSession()->set('phpBinary', $phpBinary);

            return $this->redirectToRoute($shuweiLocation === null ? 'install' : 'update');
        }

        return $this->render('php_config.html.twig', [
            'phpBinary' => $request->getSession()->get('phpBinary', $this->binaryFinder->find()),
            'shuweiLocation' => $shuweiLocation,
        ]);
    }
}
