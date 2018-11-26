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

namespace Acme\App\Test\Framework;

use Acme\App\Infrastructure\Framework\Symfony\CliApplication;
use Acme\App\Infrastructure\Framework\Symfony\CliKernel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ResettableContainerInterface;

final class CliTestKernel extends CliKernel
{
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function shutdown(): void
    {
        if ($this->container instanceof ResettableContainerInterface) {
            $this->container->reset();
        }

        if ($this->isBooted === false) {
            return;
        }

        $this->isBooted = false;

        $this->container = null;
        $this->resetServices = false;
    }

    public function getCliCommand(string $commandName): Command
    {
        return $this->container->get(CliApplication::class)->find($commandName);
    }
}
