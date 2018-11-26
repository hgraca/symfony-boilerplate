<?php

declare(strict_types=1);

return [
    Acme\App\Infrastructure\Framework\Symfony\CompilerPass\CommandCollectorCompilerPass::class => ['all' => true],
    Acme\App\Test\Framework\CompilerPass\CreateTestContainer\CreateTestContainerCompilerPass::class => ['test' => true],
];
