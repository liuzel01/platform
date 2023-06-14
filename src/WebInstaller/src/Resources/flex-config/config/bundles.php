<?php
declare(strict_types=1);

$bundles = [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
    Symfony\Bundle\DebugBundle\DebugBundle::class => ['dev' => true],
    Shuwei\Core\Framework\Framework::class => ['all' => true],
    Shuwei\Core\System\System::class => ['all' => true],
    Shuwei\Core\Content\Content::class => ['all' => true],
    Shuwei\Core\Checkout\Checkout::class => ['all' => true],
    Shuwei\Core\Maintenance\Maintenance::class => ['all' => true],
    Shuwei\Core\DevOps\DevOps::class => ['e2e' => true],
    Shuwei\Core\Profiling\Profiling::class => ['all' => true],
    Shuwei\Administration\Administration::class => ['all' => true],
    Shuwei\Elasticsearch\Elasticsearch::class => ['all' => true],
    Shuwei\Storefront\Storefront::class => ['all' => true],
];

if (class_exists(Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle::class)) {
    $bundles[Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle::class] = ['all' => true];
}

if (class_exists(Enqueue\Bundle\EnqueueBundle::class)) {
    $bundles[Enqueue\Bundle\EnqueueBundle::class] = ['all' => true];
}

if (class_exists(Enqueue\MessengerAdapter\Bundle\EnqueueAdapterBundle::class)) {
    $bundles[Enqueue\MessengerAdapter\Bundle\EnqueueAdapterBundle::class] = ['all' => true];
}

return $bundles;
