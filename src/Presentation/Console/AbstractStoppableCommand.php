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

namespace Acme\App\Presentation\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractStoppableCommand extends AbstractCommandStopwatchDecorator
{
    /**
     * @var bool
     */
    private $terminateSignalReceived = false;

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->catchKillSignals();
    }

    protected function catchKillSignals(): void
    {
        pcntl_signal(SIGHUP, [$this, 'terminate']); // 1
        pcntl_signal(SIGINT, [$this, 'terminate']); // 2
        pcntl_signal(SIGQUIT, [$this, 'terminate']); // 3
        pcntl_signal(SIGTERM, [$this, 'terminate']); // 15
    }

    protected function shouldTerminate(): bool
    {
        return $this->terminateSignalReceived;
    }

    public function terminate(): void
    {
        $this->terminateSignalReceived = true;
    }
}
