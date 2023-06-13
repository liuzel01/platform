<?php declare(strict_types=1);

use Composer\InstalledVersions;

$bundles = [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Symfony\Bundle\DebugBundle\DebugBundle::class => ['dev' => true, 'test' => true],
    Shuwei\Core\Framework\Framework::class => ['all' => true],
    Shuwei\Core\System\System::class => ['all' => true],
    Shuwei\Core\DevOps\DevOps::class => ['all' => true],
    Shuwei\Core\Maintenance\Maintenance::class => ['all' => true],
    Shuwei\Administration\Administration::class => ['all' => true],
];

if (InstalledVersions::isInstalled('symfony/web-profiler-bundle')) {
    $bundles[Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class] = ['dev' => true, 'test' => true, 'phpstan_dev' => true];
}
return $bundles;
