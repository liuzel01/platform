<?php declare(strict_types=1);

namespace Shuwei\Core\DevOps\StaticAnalyze\PHPStan;

use Shuwei\Core\DevOps\StaticAnalyze\StaticAnalyzeKernel;
use Shuwei\Core\Framework\Plugin\KernelPluginLoader\StaticKernelPluginLoader;
use Symfony\Bundle\FrameworkBundle\Console\Application;

$classLoader = require __DIR__ . '/phpstan-bootstrap.php';

$pluginLoader = new StaticKernelPluginLoader($classLoader);

$kernel = new StaticAnalyzeKernel('phpstan_dev', true, $pluginLoader, 'phpstan-test-cache-id');
$kernel->boot();

return new Application($kernel);
