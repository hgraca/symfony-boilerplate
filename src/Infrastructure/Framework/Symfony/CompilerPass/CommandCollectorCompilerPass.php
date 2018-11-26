<?php

declare(strict_types=1);

/*
 * This file is part of my Symfony boilerplate,
 * following the Explicit Architecture principles.
 *
 * @link https://herbertograca.com/2017/11/16/explicit-architecture-01-ddd-hexagonal-onion-clean-cqrs-how-i-put-it-all-together
 * @link https://herbertograca.com/2018/07/07/more-than-concentric-layers/
 *
 * (c) Herberto Graça
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acme\App\Infrastructure\Framework\Symfony\CompilerPass;

use Acme\App\Infrastructure\Framework\Symfony\CliApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class CommandCollectorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $applicationDefinition = $containerBuilder->getDefinition(CliApplication::class);

        foreach ($containerBuilder->getDefinitions() as $name => $definition) {
            $class = $definition->getClass();
            if (!is_a($class, Command::class, true)) {
                continue;
            }

            $applicationDefinition->addMethodCall('add', [new Reference($name)]);
        }
    }
}
